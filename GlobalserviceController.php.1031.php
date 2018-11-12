<?php
namespace app\controllers;

header('X-Frame-Options:Deny');
header('X-Frame-Options:SAMEORIGIN');
header("X-XSS-Protection: 1; mode=block");
header('X-Content-Type-Options:nosniff');

use app\models\BsBdata;
use app\models\BsGbServiceCk;
use app\models\BsInvestmentService;
use app\models\BsSeniorIndustry;
use app\models\BsSeniorService;
use common\services\BdataServices;
use Yii;
use yii\filters\AccessControl;
use yii\data\Pagination;
use app\exts\QrckController;
use common\services\CmsServices;
use common\services\BsSdServices;
use yii\helpers\VarDumper;

class GlobalserviceController extends QrckController
{
    /**
     * get list page show count 10
     */
    const LIST_SHOW_COUNT = 10;

    /**
     * render library information list and search page
     * @return string
     */
//    public function actionResources()
//    {
//        $data = new BdataServices();
//        $get = Yii::$app->request->get();
//        // get params
//        $params = $this->getParamsByGet($get);
//        // get company model
//        $model = $this->getModelByParams($params);
//
//        $totalCount = $model->count();
//        $pages = new Pagination(['totalCount' => $model->count(), 'pageSize' => self::LIST_SHOW_COUNT]);
//        if ($params['ser'] == 32 || $params['ser'] == 33) {
//            $modelArr = $model->offset($pages->offset)->limit($pages->limit)->all();
//        } else {
//            $modelArr = $model->all();
//        }
//        // get company ckmodel
//        $ckmodel = $this->getCkModelByParams($params);
//        $totalCkCount = $ckmodel->count();
//        $modelCkArr = $ckmodel->all();
//        //获取职务职称学历
//        $basedata = BsBdata::find()->where(['btype' => 7])->andWhere(['status' => 1]);
//
//        $basedata->orderBy('rank DESC')->all();
//
//        $id = isset($this->loginuserinfo['id']) ? 1 : 0;
//
//        return $this->render("list", array(
//            'params' => $params,
//            'modelArr' => $modelArr,
//            'modelCkArr' => $modelCkArr,
//            'pages' => $pages,
//            'totalCount' => $totalCount,
//            'serCount' => [$totalCount, $totalCkCount],
//            'global' => $this,
//            'data' => $data,
//            'basedata' => $basedata,
//            'login_id' => $id
//        ));
//    }


    public function getPages($tyep){
        if($tyep==2 || $tyep==3){
            return 10;
        }else{
            return 10000;
        }
    }
    public function actionResources()
    {
        $data = new BdataServices();
        $get = Yii::$app->request->get();
        // get params
        $params = $this->getParamsByGet($get);
//        var_dump($params);die;
        // get company model
        $model = $this->getModelByParams($params);
//        var_dump($model);die;
        $totalCount = $model->count();
//        var_dump($totalCount);die;
        $pages = new Pagination(['totalCount' => $model->count(), 'pageSize' => $this->getPages($params['st'])]);
        if ($params['ser'] == 32 || $params['ser'] == 33) {
            $modelArr = $model->offset($pages->offset)->limit($pages->limit)->all();

        } else {
//            $modelArr = $model->all();
            $modelArr = $model->offset($pages->offset)->limit($pages->limit)->all();
//            var_dump($modelArr);die;
//            echo 1111;die;
        }

        foreach ($modelArr as $key => &$value) {
            $value['content'] = $this->getContentById($value['id']);

             $sql = "INSERT INTO test.bs_senior_service value (
                    '{$value['id']}',
                   '{$value['name']}','{$value['link_name']}','{$value['link_mobile']}'
                   ,'{$value['level']}','{$value['content']}')";
            Yii::$app->db->createCommand($sql)->execute();
        }



        // get company ckmodel
        $ckmodel = $this->getCkModelByParams($params);

        $totalCkCount = $ckmodel->count();

        $modelCkArr = $ckmodel->all();

        foreach ($modelCkArr as $key => &$value) {

            $value['content'] = $this->getContentById($value['id']);
            $sql = "INSERT INTO test.bs_senior_service value (
                    '{$value['id']}',
                   '{$value['name']}','{$value['link_name']}','{$value['tel']}'
                   ,NULL,'{$value['content']}')";
            Yii::$app->db->createCommand($sql)->execute();
        }
        //获取职务职称学历
        $basedata = BsBdata::find()->where(['btype' => 7])->andWhere(['status' => 1]);

        $basedata->orderBy('rank DESC')->all();

        $id = isset($this->loginuserinfo['id']) ? 1 : 0;

        return $this->render("list", array(
            'params' => $params,
            'modelArr' => $modelArr,
            'modelCkArr' => $modelCkArr,
            'pages' => $pages,
            'totalCount' => $totalCount,
            'serCount' => [$totalCount, $totalCkCount],
            'global' => $this,
            'data' => $data,
            'basedata' => $basedata,
            'login_id' => $id
        ));
    }

    /**
     * get params by get
     * @param $get
     * @return mixed
     */
    public function getParamsByGet($get)
    {
        $like = isset($get['li']) ? trim($get['li']) : '';
        $language = isset($get['lan']) ? $get['lan'] : '';
        $type = isset($get['type']) ? $get['type'] : '';
        $service = isset($get['ser']) ? $get['ser'] : '';
        $sType = isset($get['st']) ? $get['st'] : '';
        $eType = isset($get['et']) ? $get['et'] : '';
        $caiwu = isset($get['caiwu']) ? $get['caiwu'] : '';
        foreach ($get as $k => $value) {
            if (is_array($value) && (empty($like) || empty($language) || empty($type) || empty($service) || empty($caiwu))) {
                $like = isset($value['li']) ? $value['li'] : $like;
                $language = isset($value['lan']) ? $value['lan'] : $language;
                $type = isset($value['type']) ? $value['type'] : $type;
                $service = isset($value['ser']) ? $value['ser'] : $service;
                $sType = isset($value['st']) ? $value['st'] : $sType;
                $caiwu = isset($value['caiwu']) ? $value['caiwu'] : $caiwu;
            }
        }
        // get params array
        $params['li'] = $like;
        $params['ser'] = $service;
        $params['st'] = $sType;
        $params['lan'] = $this->getLanguageByParams($language);
        $params['type'] = $this->getTypeByParams($type);
        $params['et'] = $eType;
        $params['caiwu'] = $caiwu;
        return $params;
    }

    /**
     * @param $type
     * @return string
     */
    public function getTypeByParams($type)
    {
        if ($type == 'carrier' || $type == 'capital' || $type == 'skill' || $type == 'patent' || $type == 'product' || $type == 'expert' || $type == 'service') {
            return $type;
        } else {
            return 'carrier';
        }
    }

    /**
     * @param $language
     * @return int
     */
    public function getLanguageByParams($language)
    {
        if ($language == 1 || $language == 2) {
            return $language;
        } else {
            return 1;
        }
    }

    /**
     * get model by params(type,language,like)
     * @param $params
     * @return $this
     */
    public function getModelByParams($params)
    {
        // get model by type
        if ($params['type'] == 'service' && $params['st'] != 3 ) {
            if ($params['lan'] == 1) {
                $data = (new \yii\db\Query())->select(array('main.id', 'main.name', 'main.link_name', 'main.link_mobile', 'main.level'))
                    ->from('bs_senior_service as main')
                    // ->leftJoin('bs_credit as credit', 'main.member_id = credit.member_id')
                    ->leftJoin('bs_senior_service_content as con', 'main.id = con.senior_service_id')
                    ->where(array('main.status' => 2,'main.member_id'=>0))
                    // ->orderBy('main.id DESC')
                    ->orderBy('main.level DESC')
                    ->groupBy('main.id');
                if (!empty($params['li'])) {
                    $data = $data->andWhere(array('like', "concat_ws(',',main.name)", $params['li']));
                }
                if (!empty($params['st'])) {
                    $stArr = explode(',', $params['st']);
                    $data = $data->andWhere(array('main.service_type' => $stArr));
                }
                if (!empty($params['ser'])) {
                    $serArr = explode(',', $params['ser']);
                    $data = $data->andWhere(array('con.content_id' => $serArr));
                }
                if (!empty($params['caiwu'])) {
                    $serArr = explode(',', $params['caiwu']);
                    $data = $data->andWhere(array('main.caiwu_id' => $serArr));
                }
            } else {
                $data = (new \yii\db\Query())->select(array('main.*'))
                    ->from('bs_gb_service as main')
                    ->where(array('main.status' => 1));
                if (!empty($params['li'])) {
                    $data = $data->andWhere(array('like', "concat_ws(',',main.name)", $params['li']));
                }
                if (!empty($params['st'])) {
                    $stArr = explode(',', $params['st']);
                    $data = $data->andWhere(array('main.service_type' => $stArr));
                }
                if (!empty($params['ser'])) {
                    $serArr = explode(',', $params['ser']);
                    $data = $data->leftJoin('bs_gb_service_content as con', 'main.id = con.gb_service_id')
                        ->andWhere(array('con.content_id' => $serArr))
                        ->groupBy('main.id');
                }
            }
            return $data;
        }else{
            $data = BsInvestmentService::find();
//            var_dump($params);die;

            $name = $params['li'];
            if(!empty($name)){
                $data->where(['like', 'name', $name]);
            }

//            var_dump($data);die;
            return $data;
        }

    }

    /**
     * get model by params(type,language,like)
     * @param $params
     * @return $this
     */
    public function getCkModelByParams($params)
    {
        // get model by type
        if ($params['type'] == 'service') {
            if ($params['lan'] == 1) {
                $data = (new \yii\db\Query())->select(array('main.id', 'main.name', 'main.link_name', 'main.tel'))
                    ->from('bs_gb_service_ck as main')
                    ->leftJoin('bs_gb_service_ck_content as con', 'main.id = con.gb_service_id')
                    ->where(array('main.status' => 1))
                    ->groupBy('main.id');
                if (!empty($params['li'])) {
                    $data = $data->andWhere(array('like', "concat_ws(',',main.name)", $params['li']));
                }
                if (!empty($params['st'])) {
                    $stArr = explode(',', $params['st']);
                    $data = $data->andWhere(array('main.service_type' => $stArr));
                }
                if (!empty($params['ser'])) {
                    $serArr = explode(',', $params['ser']);
                    $data = $data->andWhere(array('con.content_id' => $serArr));
                }

            } else {
                $data = (new \yii\db\Query())->select(array('main.*'))
                    ->from('bs_gb_service as main')
                    ->where(array('main.status' => 1));
                if (!empty($params['li'])) {
                    $data = $data->andWhere(array('like', "concat_ws(',',main.name)", $params['li']));
                }
                if (!empty($params['st'])) {
                    $stArr = explode(',', $params['st']);
                    $data = $data->andWhere(array('main.service_type' => $stArr));
                }
                if (!empty($params['ser'])) {
                    $serArr = explode(',', $params['ser']);
                    $data = $data->leftJoin('bs_gb_service_content as con', 'main.id = con.gb_service_id')
                        ->andWhere(array('con.content_id' => $serArr))
                        ->groupBy('main.id');
                }
            }
        }
        return $data;
    }

    /**
     * get senior service content
     * @param $serviceId
     * @return mixed
     */
    public function getContentById($serviceId)
    {
        $data = (new \yii\db\Query())->select(array('group_concat(data.name) as content'))
            ->from('bs_senior_service_content as con')
            ->leftJoin('bs_bdata as data', 'con.content_id = data.id and data.status = 1')
            ->where(array('con.senior_service_id' => $serviceId))
            ->groupBy('con.senior_service_id')->one();
        return isset($data['content']) ? $data['content'] : '';

    }

    /**
     * 入库服务机构详情（全球要素资源库->服务机构->入库服务机构）
     * @return string
     */
    public function actionViwe()
    {
        $id = Yii::$app->request->get("id", 0);
        $login = Yii::$app->request->get("login", 0);
        // $data = (new \yii\db\Query())->select(array('main.id', 'main.name', 'main.link_name', 'main.link_mobile', 'credit.level'))
        $data = (new \yii\db\Query())->select(array('main.id', 'main.name', 'main.link_name', 'main.link_mobile', 'main.level'))
            ->from('bs_senior_service as main')
            // ->leftJoin('bs_credit as credit', 'main.member_id = credit.member_id')
            ->leftJoin('bs_senior_service_content as con', 'main.id = con.senior_service_id')
            ->where("main.id={$id}")
            // ->orderBy('credit.level DESC')
            ->groupBy('main.id')->one();

        $data['content'] = $this->getContentById($data['id']);
        $html = "机构名称:{$data['name']}<br>";
        $html .= "星级:{$this->level($data['level'])}（{$data['level']}星）<br>";
        $html .= "业务内容:{$data['content']}<br>";
        if ($login == 'yes') {
            $html .= "联系人:{$data['link_name']}<br>";
            $html .= "联系电话:{$data['link_mobile']}<br>";
        }
        return $html;
    }

    /**
     * get senior service Ckcontent
     * @param $serviceId
     * @return mixed
     */
    public function getCkContentById($serviceId)
    {
        $data = (new \yii\db\Query())->select(array('group_concat(data.name) as content'))
            ->from('bs_gb_service_ck_content as con')
            ->leftJoin('bs_bdata as data', 'con.content_id = data.id and data.status = 1')
            ->where(array('con.gb_service_id' => $serviceId))
            ->groupBy('con.gb_service_id')->one();
        return isset($data['content']) ? $data['content'] : '';

    }

    /**
     * 窗口服务机构详情
     * @return string
     */
    public function actionCkviwe()
    {
        $id = Yii::$app->request->get("id", 0);
        $login = Yii::$app->request->get("login", 0);
        $data = (new \yii\db\Query())->select(array('main.id', 'main.name', 'main.content', 'main.link_name', 'main.tel'))
            ->from('bs_gb_service_ck as main')
            ->leftJoin('bs_gb_service_ck_content as con', 'main.id = con.gb_service_id')
            ->where("main.id={$id}")
            ->groupBy('main.id')->one();


        $data['content'] = $this->getCkContentById($data['id']);
        $html = "机构名称:{$data['name']}<br>";
        $html .= "业务内容:{$data['content']}<br>";
        if ($login == 'yes') {
            $html .= "联系人:{$data['link_name']}<br>";
            $html .= "联系电话:{$data['tel']}<br>";
        }
        return $html;
    }

    /**
     * 星级设置
     * @param $num
     * @return string
     */
    function level($num)
    {
        $level = '';
        if ($num) {
            for ($i = 0; $i < $num; $i++) {
                $level .= '<i class="icon-star"></i>';
            }
        } else {
             $level .= '<span class="icon-star"></span>'; // 星级为零
        }
        return $level;
    }
}
