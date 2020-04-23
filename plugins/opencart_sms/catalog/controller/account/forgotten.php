<?php

class ControllerAccountForgotten extends Controller
{
	private $error = array();

	public function index()
	{
		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/account'));
		}

		$this->load->language('account/forgotten');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {


			if ($this->request->post['type'] == 'mobile') {
				$this->checksms($this->request->post['telephone']);
				$customer_info = $this->model_account_customer->getCustomerByMobile($this->request->post['telephone']);
				$this->model_account_customer->editPassword($customer_info['customer_id'], $this->session->data['checkpassword']);
				$this->session->data['success'] = '重设密码链接已发送到您的手机！';
			} else {

				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
				$this->model_account_customer->editCode($customer_info['customer_id'], token(40));
				$this->session->data['success'] = $this->language->get('text_success');

			}


			$this->response->redirect($this->url->link('account/login'));
		}

		if (isset($this->request->post['type'])) {
			$data['register_type'] = $this->request->post['type'];
		} else {
			$data['register_type'] = 'mobile';
		}
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_forgotten'),
			'href' => $this->url->link('account/forgotten')
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['mobiles'])) {
			$data['error_mobiles'] = $this->error['mobiles'];
		} else {
			$data['error_mobiles'] = '';
		}

		$data['action'] = $this->url->link('account/forgotten');

		$data['back'] = $this->url->link('account/login');

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}
		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/forgotten', $data));
	}


	protected function checksms($mobile)
	{
		$this->load->model('setting/setting');
		$data['appid'] = $this->config->get('config_appid');
		$data['signature'] = $this->config->get('config_appkey');
		$data['to'] =   $mobile;

		$sign = $this->config->get('config_appsign');
		if (empty($data['appid']) && empty($data['signature']) && empty($sign)) {
			return 2;
			exit;
		}
		$smsapi = "https://api.mysubmail.com/message/send/";
		$code = rand(1000, 9999);
		$data['content'] = urlencode('【' . $sign . '】您的新密码为:' . $code . '请妥善保存!');
		$query = http_build_query($data);
		$options['http'] = array(
			'timeout' => 60,
			'method' => 'POST',
			'header' => 'Content-type:application/x-www-form-urlencoded',
			'content' => $query
		);
		$context = stream_context_create($options);
		$result = file_get_contents($smsapi, false, $context);
		$output = trim($result, "\xEF\xBB\xBF");
		$res = json_decode($output, true);

		if ($res['status']== 'success') {
			$this->session->data['checkpassword'] = $code;
			return 0;
			exit;
		} else {
			return $res;
			exit;
		}
	}


	protected function validate()
	{


		if ($this->request->post['type'] == 'mobile') {

			if ($this->request->post['mobiles'] != $this->session->data['mobile_code']) {
				$this->error['mobiles'] = '短信验证码错误';
			}


			if ($this->request->post['telephone'] && !$this->model_account_customer->getTotalCustomersByTelephone($this->request->post['telephone'])) {
				$this->error['warning'] = $this->language->get('error_exists_telephone');

			}

		}


		if (!isset($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_email');
		} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_email');
		}

		// Check if customer has been approved.
		$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

		if ($customer_info && !$customer_info['status']) {
			$this->error['warning'] = $this->language->get('error_approved');
		}

		return !$this->error;
	}
}
