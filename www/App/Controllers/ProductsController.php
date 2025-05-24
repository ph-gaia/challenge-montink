<?php

namespace App\Controllers;

use Core\Controller\AbstractController;
use Core\Interfaces\ControllerInterface;
use App\Models\ProductsModel;
use App\Helpers\Message;
use Core\Http\Header;

class ProductsController extends AbstractController implements ControllerInterface
{
    private ProductsModel $model;

    private Header $header;

    public function __construct(array $parameters)
    {
        parent::__construct($parameters);
        $this->model = new ProductsModel();
        $this->header = new Header();
    }

    public function index(): void
    {
        $this->view->title = "Produtos";

        $this->view->products = $this->model->getAllProducts();

        $this->render("Index");
    }

    public function edit(): void
    {
        $this->view->title = "Editar Produto";
        $id = $this->getParam('id');
        $this->view->product = $this->model->getProductById($id);
        $this->view->products = $this->model->getAllProducts();
        $this->render('Index');
    }

    public function save()
    {
        $data = $this->getPostData();
        
        if (empty($data['name']) || empty($data['price']) || !isset($data['stock'])) {
            $this->header->setHttpHeader(400);
            Message::showMsg("Por favor, preencha todos os campos obrigatórios.", "danger");
            return;
        }

        $productData = [
            'name' => $data['name'],
            'price' => (float) $data['price'],
            'stock' => (int) $data['stock']
        ];

        if (!empty($data['id'])) {
            $productData['id'] = (int) $data['id'];
            $this->model->updateProduct($productData);
        } else {
            $productId = $this->model->createProduct($productData);
        }

        if (!empty($data['variations'])) {
            $variations = $data['variations'];
            foreach ($variations as $variation) {
                if (empty($variation['name']) || !isset($variation['stock'])) {
                    continue;
                }

                $variationData = [
                    'product_id' => $productId ?? $data['id'],
                    'name' => $variation['name'],
                    'price_override' => (float) $data['price'],
                    'stock' => (int) $variation['stock']
                ];

                if (!empty($variation['id'])) {
                    $variationData['id'] = (int) $variation['id'];
                    $this->model->updateVariation($variationData);
                } else {
                    $this->model->createVariation($variationData);
                }
            }
        }

        $this->header->setHttpHeader(201);
        Message::showMsg("Produto salvo com sucesso.", "success", false);
        return;
    }

    private function getPostData(): array
    {
        return $_POST;
    }
}
