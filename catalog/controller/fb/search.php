<?php 
class ControllerFbSearch extends Controller { 	
	
	public function index(){
		
		$this->language->load('product/search');
		$this->language->load('fb/search');
        
		$this->data['text_sort'] = $this->language->get('text_sort');
        $this->data['text_no_result'] = $this->language->get('text_no_result');
		
        $this->data['products'] = array();
        
    	if (isset($this->request->get['keyword'])) {
            $this->data['heading_title'] = $this->document->title = sprintf($this->language->get('heading_title'),$this->request->get['keyword']);
        } else {
            $this->data['heading_title'] = $this->document->title = $this->language->get('heading_title');
        }
        
		if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.sort_order';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }
        
		if (isset($this->request->get['description'])) {
            $this->data['description'] = $this->request->get['description'];
        } else {
            $this->data['description'] = '';
        }
        
        if (isset($this->request->get['model'])) {
            $this->data['model'] = $this->request->get['model'];
        } else {
            $this->data['model'] = '';
        }
        
        if (isset($this->request->get['keyword'])) {
        	
        	$this->load->model('catalog/product');
            
            $product_total = $this->model_catalog_product->getTotalProductsByKeyword($this->request->get['keyword'], '', isset($this->request->get['description']) ? $this->request->get['description'] : '', isset($this->request->get['model']) ? $this->request->get['model'] : '');

            $product_tag_total = 0;//$this->model_catalog_product->getTotalProductsByTag($this->request->get['keyword'], '');
            
            $product_total = max($product_total, $product_tag_total);
        	
        	if ($product_total) { 
                $url = '';

                if (isset($this->request->get['description'])) {
                    $url .= '&description=' . $this->request->get['description'];
                }
                
                if (isset($this->request->get['model'])) {
                    $url .= '&model=' . $this->request->get['model'];
                }
                
                $this->load->model('catalog/review');
                $this->load->model('tool/seo_url'); 
                $this->load->model('tool/image');
                
                $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
                
                $this->data['products'] = array();
                
                $results = $this->model_catalog_product->getProductsByKeyword($this->request->get['keyword'], '', isset($this->request->get['description']) ? $this->request->get['description'] : '', isset($this->request->get['model']) ? $this->request->get['model'] : '', $sort, $order, ($page - 1) * $this->config->get('config_catalog_limit'), $this->config->get('config_catalog_limit'));

                $tag_results = array();//$this->model_catalog_product->getProductsByTag($this->request->get['keyword'], '', $sort, $order, ($page - 1) * $this->config->get('config_catalog_limit'), $this->config->get('config_catalog_limit'));
                
                foreach ($results as $key => $value) {
                    $tag_results[$value['product_id']] = $results[$key];
                }
                
                //$product_total = count($tag_results);
                
                foreach ($tag_results as $result) {
                    if ($result['image']) {
                        $image = $result['image'];
                    } else {
                        $image = 'no_image.jpg';
                    }						
                    
                    if ($this->config->get('config_review')) {
                        $rating = $this->model_catalog_review->getAverageRating($result['product_id']);	
                    } else {
                        $rating = false;
                    }
                    
                    $special = FALSE;
                    
                    $discount = $this->model_catalog_product->getProductDiscounts($result['product_id']);
                    
                    if ($discount) {
                        $price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
                    } else {
                        $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                        
                        $special = $this->model_catalog_product->getProductSpecials($result['product_id']);
                        
                        if ($special) {
                            $special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
                        }					
                    }
                    
                    $options = $this->model_catalog_product->getProductOptions($result['product_id']);
                    
                    if ($options) {
                        $add = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=lite/product&product_id=' . $result['product_id']);
                    } else {
                        $add = HTTPS_SERVER . 'index.php?route=fb/cart&product_id=' . $result['product_id'];
                    }
                    
                    $desc = strlen($result['description']) > 500 ? substr($result['description'],0,500).'...' : $result['description'];
                    
                    $this->data['products'][] = array(
                    	'product_id'	=> $result['product_id'],
                        'name'    => $result['name'],
                    	'description' => html_entity_decode($desc, ENT_QUOTES, 'UTF-8'),
                        'model'   => $result['model'],
                        'rating'  => $rating,
                        'stars'   => sprintf($this->language->get('text_stars'), $rating),
                        'thumb'   => $this->model_tool_image->resize($image, 120, 120),
                        'price'   => $price,
                        'options' => $options,
                        'special' => $special,
                        'href'    => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=fb/product&keyword=' . $this->request->get['keyword'] . $url . '&product_id=' . $result['product_id']),
                        'add'	  => $add
                    );
                }
                
                if (!$this->config->get('config_customer_price')) {
                    $this->data['display_price'] = TRUE;
                } elseif ($this->customer->isLogged()) {
                    $this->data['display_price'] = TRUE;
                } else {
                    $this->data['display_price'] = FALSE;
                }
                
                $url = '';
                
                if (isset($this->request->get['keyword'])) {
                    $url .= '&keyword=' . $this->request->get['keyword'];
                }
                
                if (isset($this->request->get['category_id'])) {
                    $url .= '&category_id=' . $this->request->get['category_id'];
                }
                
                if (isset($this->request->get['description'])) {
                    $url .= '&description=' . $this->request->get['description'];
                }
                
                if (isset($this->request->get['model'])) {
                    $url .= '&model=' . $this->request->get['model'];
                }

                if (isset($this->request->get['page'])) {
                    $url .= '&page=' . $this->request->get['page'];
                }	
                
                $this->data['sorts'] = array();
                
                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_default'),
                    'value' => 'p.sort_order-ASC',
                    'href'  => HTTP_SERVER . 'index.php?route=fb/search' . $url . '&sort=p.sort_order&order=ASC'
                );
                
                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_name_asc'),
                    'value' => 'pd.name-ASC',
                    'href'  => HTTP_SERVER . 'index.php?route=fb/search' . $url . '&sort=pd.name&order=ASC'
                ); 

                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_name_desc'),
                    'value' => 'pd.name-DESC',
                    'href'  => HTTP_SERVER . 'index.php?route=fb/search' . $url . '&sort=pd.name&order=DESC'
                );

                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_price_asc'),
                    'value' => 'p.price-ASC',
                    'href'  => HTTP_SERVER . 'index.php?route=fb/search' . $url . '&sort=p.price&order=ASC'
                ); 

                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_price_desc'),
                    'value' => 'p.price-DESC',
                    'href'  => HTTP_SERVER . 'index.php?route=fb/search' . $url . '&sort=p.price&order=DESC'
                ); 
                
                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_rating_desc'),
                    'value' => 'rating-DESC',
                    'href'  => HTTP_SERVER . 'index.php?route=fb/search' . $url . '&sort=rating&order=DESC'
                ); 
                
                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_rating_asc'),
                    'value' => 'rating-ASC',
                    'href'  => HTTP_SERVER . 'index.php?route=product/search' . $url . '&sort=rating&order=ASC'
                );
                
                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_model_asc'),
                    'value' => 'p.model-ASC',
                    'href'  => HTTP_SERVER . 'index.php?route=fb/search' . $url . '&sort=p.model&order=ASC'
                ); 

                $this->data['sorts'][] = array(
                    'text'  => $this->language->get('text_model_desc'),
                    'value' => 'p.model-DESC',
                    'href'  => HTTP_SERVER . 'index.php?route=fb/search' . $url . '&sort=p.model&order=DESC'
                );
                
                $url = '';

                if (isset($this->request->get['keyword'])) {
                    $url .= '&keyword=' . $this->request->get['keyword'];
                }
                
                if (isset($this->request->get['category_id'])) {
                    $url .= '&category_id=' . $this->request->get['category_id'];
                }
                
                if (isset($this->request->get['description'])) {
                    $url .= '&description=' . $this->request->get['description'];
                }
                
                if (isset($this->request->get['model'])) {
                    $url .= '&model=' . $this->request->get['model'];
                }
                
                if (isset($this->request->get['sort'])) {
                    $url .= '&sort=' . $this->request->get['sort'];
                }	

                if (isset($this->request->get['order'])) {
                    $url .= '&order=' . $this->request->get['order'];
                }
                
                $pagination = new Pagination();
                $pagination->total = $product_total;
                $pagination->page = $page;
                $pagination->limit = $this->config->get('config_catalog_limit');
                $pagination->text = $this->language->get('text_pagination');
                $pagination->url = HTTP_SERVER . 'index.php?route=fb/search' . $url . '&page={page}';
                
                $this->data['pagination'] = $pagination->render();
                
                $this->data['sort'] = $sort;
                $this->data['order'] = $order;
            }
            
	       	$this->template = 'fb/template/product/search.tpl';
	        
	        $this->children = array(
	            'fb/footer',
	            'fb/header'
	        );
	        
	        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        	
        }
        
	}
}