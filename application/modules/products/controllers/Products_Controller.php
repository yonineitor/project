<?php
use Core\Controller\Clean_Controller;
use Lib\Response;

/**
 * 
 */
class Products_Controller extends Clean_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->template->setLayout("demo");
	}

	//listado de productos
	public function search()
	{
		$Products = file_get_contents("http://test.oneclean.mx/test.json");
		
		$this->template->js('/front/js/products/products-search.js' );
		
		//$this->template->tit
		return $this->template->render('view-products-search', [
			'products' => $Products
		]);
	}

	//carrito
	public function cart()
	{

		$Products = file_get_contents("http://test.oneclean.mx/test.json");
		
		$this->template->js('/front/js/products/products-cart.js' );
		
		return $this->template->render('view-products-cart',[
			'products' => $Products
		]);
	}

	//intentar pago
	public function tryToPay()
	{
		$url = "http://test.oneclean.mx/try.php";
		
		//$curl
		if(!is_array($this->input->post('products')) || !count($this->input->post('products')) )
			return Response::json([
				'message' => 'Invalid products'
			]);
		
		$postString = json_encode([
			'products' => $this->input->post('products')
		]);
		
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_POSTFIELDS => $postString
        );
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            $options[CURLOPT_SAFE_UPLOAD] = true;
        }

        curl_setopt_array($ch, $options);
        $curlExecute = curl_exec($ch);
        if ( $curlExecute === false) 
        {
            $curlErrno = curl_errno($ch);
  			$curlError = curl_error($ch);
			
			return Response::json([
				'status' => 0,
				'message' => $curlErrno,
				'error' => $curlError,
				'postString ' => $postString 
			]);            
        }
		
		curl_close($ch);
		
		$decode = @json_decode($curlExecute);

		if($decode->message !== 'OK')
		{
			return Response::json([
				'message' => $decode->message,
			]);
		}

		return Response::json([
			'status' => 1,
			'message' => 'OK',
		]);
	}
	
}