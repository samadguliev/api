<?php

namespace App\Common\Model;
use Carbon\Carbon;
use \Bitrix\Main\Loader;
Loader::includeModule('arealidea.ipm');

class Base extends \Akop\Element\DbElement
{
    protected $editRole = '';
    protected $viewRole = '';
    protected $dates = [];
    protected $accessManager =  null;
    protected $primaryKey =  'id';
    private $isArchived = false;

    public function __construct()
    {
        parent::__construct();
        $this->accessManager = new \Arealidea\Ipm\AccessManager($this->projectId);
    }

    public function getList(array $params = array())
    {
        if ($this->isViewable()) {
            return parent::getList($params);
        }
        throw new \Exception('Access denied', 403);
    }

    public function add(array $params)
    {
        if ($this->isEditable()) {
          if ($id = parent::add($params)) {
              return $id;
          }
          return false;
        }
        throw new \Exception('Access denied', 403);
    }

    public function delete($primaryKey)
    {
        if ($this->isEditable()) {
            if (parent::delete($primaryKey)) {
                return true;
            }
            return false;
        }
        throw new \Exception('Access denied', 403);
    }

    public function update($primaryKey, array $params)
    {
        if ($this->isEditable()) {
            return parent::update($primaryKey, $params);
        }
        throw new \Exception('Access denied', 403);
    }

    public function getPermissions()
    {
        return [
            'view' => $this->isViewable(),
            'edit' => $this->isEditable(),
        ];
    }

    protected function isEditable()
    {
        if (empty($this->editRole)) {
            return true;
        }
        return $this->accessManager->get($this->editRole);
    }

    protected function isViewable()
    {
        if (empty($this->viewRole)) {
            return true;
        }
        // \Akop\Util::pre([$accessManager, $accessManager->get($this->viewRole)]);
        return $this->accessManager->get($this->viewRole);
    }

    private function convertDatesFromDB($params)
    {
        if (empty($this->dates)) {
            return $params;
        }
        $result = $params;
        foreach ($this->dates as $fieldname) {
            if (!empty($result[$fieldname])) {
		if (!is_string($result[$fieldname])) {
	            $result[$fieldname] = $result[$fieldname]->toString();
		}
            }
        }
        return $result;
    }

    public function archive($id)
    {
        $this->update($id, ['isArchive' => 'Y']);
        return ($this->isArchived = true);
    }

    public function isArchived()
    {
        return $this->isArchived;
    }
}
