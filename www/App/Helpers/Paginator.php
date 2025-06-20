<?php

/**
 *
 * - Helper que auxilia no gerenciamento de página onde há a necessidade do uso de Links de paginação
 */

namespace App\Helpers;

use Core\Database\ModelAbstract;

class Paginator extends ModelAbstract
{

    protected $entidade;
    private $pagina;
    private $totalResult;
    private $totalPagina;
    private $maxResult;
    private $select;
    private $btn = [];
    private $resultado = [];
    private $maxOffSet;
    private $where;
    private $orderBy;
    private $bindValue;
    private $currentPage = 1;

    public function __construct(array $dados)
    {
        parent::__construct();

        $this->setEntidade($dados['entidade'])
            ->setWhere(isset($dados['where']) ? $dados['where'] : null)
            ->setOrderBy(isset($dados['orderBy']) ? $dados['orderBy'] : null)
            ->setBindValue(isset($dados['bindValue']) ? $dados['bindValue'] : null)
            ->setMaxResult(isset($dados['maxResult']) ? $dados['maxResult'] : null)
            ->setSelect(isset($dados['select']) ? $dados['select'] : null)
            ->setTotalResult()
            ->setPagina(isset($dados['pagina']) ? $dados['pagina'] : 1)
            ->setTotalPagina()
            ->setMaxOffSet()
            ->paginator()
            ->setBtn();
    }

    private function setEntidade($entidade)
    {
        $this->entidade = $entidade;
        return $this;
    }

    private function getEntidade()
    {
        return $this->entidade;
    }

    private function setPagina($pagina)
    {
        $this->pagina = isset($pagina) ? $pagina : 1;
        if (!is_numeric($this->pagina) || $this->getPagina() > $this->getTotalResult()) {
            $this->pagina = 1;
        }
        return $this;
    }

    private function getPagina()
    {
        return $this->pagina;
    }

    private function setMaxResult($valor = null)
    {
        $this->maxResult = isset($valor) ? $valor : 20;
        if (!is_numeric($this->maxResult)) {
            $this->maxResult = 20;
        }
        return $this;
    }

    private function getMaxResult()
    {
        return $this->maxResult;
    }

    private function setSelect($valor = null)
    {
        $this->select = isset($valor) ? $valor : '*';
        return $this;
    }

    private function getSelect()
    {
        return $this->select;
    }

    private function setTotalResult()
    {
        $sql = "SELECT {$this->getSelect()} FROM {$this->getEntidade()} "
            . "{$this->getWhere()} "
            . "{$this->getOrderBy()} ;";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($this->getBindValue());
        $this->totalResult = count($stmt->fetchAll(\PDO::FETCH_ASSOC));
        return $this;
    }

    private function getTotalResult()
    {
        return $this->totalResult;
    }

    private function setTotalPagina()
    {
        $this->totalPagina = ceil($this->getTotalResult() / $this->getMaxResult());
        return $this;
    }

    private function setMaxOffSet()
    {
        $page = $this->getPagina();
        $limit = $this->getMaxResult();
        $allResults = $this->getTotalResult();
        $offset = 0;

        if ($page) {
            $lastPage = ceil($allResults / $limit);

            if ($page > $lastPage) {
                $page = $lastPage;
            }
            if ($page > 1) {
                $offset = ($page - 1) * $limit;
            }
            if ($page <= 1) {
                $offset = 0;
            }
        }

        $this->maxOffSet = $offset;
        return $this;
    }

    private function getMaxOffSet()
    {
        return $this->maxOffSet;
    }

    private function setOrderBy($orderBy = null)
    {
        $this->orderBy = ($orderBy) ? ' ORDER BY ' . $orderBy : null;
        return $this;
    }

    private function getOrderBy()
    {
        return $this->orderBy;
    }

    private function setWhere($where = null)
    {
        $this->where = ($where) ? ' WHERE ' . $where : null;
        return $this;
    }

    private function getWhere()
    {
        return $this->where;
    }

    private function setBindValue($bindValue = null)
    {
        $this->bindValue = $bindValue;
        return $this;
    }

    private function getBindValue()
    {
        return $this->bindValue = is_array($this->bindValue) ? $this->bindValue : [];
    }

    private function setBtn()
    {
        $page = $this->getPagina();
        $allResults = $this->getTotalResult();
        $limit = $this->getMaxResult();
        $lastPage = ceil($allResults / $limit);
        $chunkValue = $lastPage < 10 ? $lastPage : 10;
        $allButtons = array_keys(array_fill(1, $allResults, ''));
        $allButtonsGroup = count($allButtons) ? array_chunk($allButtons, $chunkValue) : [];

        if ($page > $lastPage) {
            $page = $lastPage;
        }

        foreach ($allButtonsGroup as $group) {
            foreach ($group as $value) {
                if ($page == $value) {
                    $this->btn = $group;
                    break;
                }
            }
        }

        $this->currentPage = $page;
        return $this;
    }

    private function getBtn()
    {
        return $this->btn;
    }

    private function getNext()
    {
        $next = $this->getPagina() + 1;
        $totalResults = count($this->resultado);
        if ($totalResults < $this->getMaxResult()) {
            return end($this->btn);
        }
        return $next;
    }

    private function getPrevious()
    {
        $previous = $this->getPagina() - 1;
        if ($previous <= 0) {
            return 1;
        }

        return $previous;
    }

    private function makeBtn()
    {
        $btn = [];
        $btn['link'] = $this->getBtn();
        $btn['previous'] = $this->getPrevious();
        $btn['next'] = $this->getNext();
        $btn['current'] = $this->currentPage;
        return $btn;
    }

    public function getNaveBtn()
    {
        if (!$this->getTotalResult()) {
            return [
                'link' => [],
                'previous' => 1,
                'next' => 1,
                'current' => 1,
            ];
        }
        return $this->btn = $this->makeBtn();
    }

    private function setResultado($resultado)
    {
        $this->resultado = $resultado;
        return $this;
    }

    public function getResultado()
    {
        return $this->resultado;
    }

    private function paginator()
    {
        $sql = "SELECT {$this->getSelect()} FROM {$this->getEntidade()} "
            . "{$this->getWhere()} "
            . "{$this->getOrderBy()} "
            . "LIMIT {$this->getMaxOffSet()},{$this->getMaxResult()} ;";
        $stmt = $this->pdo()->prepare($sql);    
        $stmt->execute($this->getBindValue());
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->setResultado($resultado);
        return $this;
    }
}
