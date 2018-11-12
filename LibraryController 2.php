<?php
namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\data\Pagination;
use app\exts\QrckController;
use common\services\CmsServices;
use common\services\BsSdServices;
use common\services\SerRoute;
use common\services\BdataServices;
use app\models\BsSeniorIndustry;

class LibraryController extends QrckController
{
    /**
     * get list page show count 10
     */
    const LIST_SHOW_COUNT = 10;

    /**
     * render library information list and search page
     * @return string
     */
    public function actionInformation()
    {
        // 协议认定参数
        $parameter = Yii::$app->request->get('deal');
        isset($parameter) ? $deal=$parameter : $deal=0; // deal=1,同意协议; deal=0,不同意协议

        if ($deal == 1) {
            // 登录及权限验证
            //$jump_url = Url::to('/library/information', true);
            //$jump_url = Url::to(['library/information']); //本地
            //$this ->loginCheck($jump_url);
            $id = $this->loginuserinfo['id'];
            $login_name = $this->loginuserinfo['login_name'];

            if($id == 3316 && $login_name == '成都乐氏') {
                $service = new BsSdServices();
                $data = new BdataServices();
                $get = Yii::$app->request->get();
                // get params
                $params = $this->getParamsByGet($get);
                // get company model
                $model = $this->getCompanyByParams($params);
                $totalCount = $model->count();
//        echo $model->createCommand()->getRawSql();die;
                $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => self::LIST_SHOW_COUNT]);
                $modelArr = $model->groupBy(array('main.id'))->offset($pages->offset)->all();
                // var_dump($modelArr);exit;
                foreach ($modelArr as $key => &$value) {
                     $value['content'] = $this->getIndustryStrById($value['id'],$value['member_id'],TRUE);
                    if(strlen($value['link_tel'])==1) $value['link_tel']='';
                    $sql = "INSERT INTO test.bs_senior_company value (
                    '{$value['id']}',
                   '{$value['name']}','{$value['reg_date']}','{$value['reg_capital']}'
                   ,\"{$value['legal_rep']}\",'{$value['link_tel']}','{$value['content']}','{$value['work_address']}','{$value['code']}')";
                   Yii::$app->db->createCommand($sql)->execute();
                }


                $industrys = $data->getDataByType(BdataServices::INDUSTRY_BTYPE_ID);
                // get industry array(id =>name)
                $industryArray = array();
                foreach ($industrys as $industry) {
                    $industryArray[$industry->id] = $industry->name;
                }

                return $this->render("information", array(
                    'params' => $params,
                    'modelArr' => $modelArr,
                    'pages' => $pages,
                    'totalCount' => $totalCount,
                    'isSenior' => $this->getIsSenior(),
                    'industryArray' => $industryArray,
                    'qualificationArr' => $this->getQualification(),
                    'service' => $service,
                ));
            } else {
                $level = $this->loginuserinfo['level'];
                $get = Yii::$app->request->get();
                $params = $this->getParamsByGet($get);
                $model = $this->getCompanyByParams($params);
                $totalCount = $model->count();
                $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => self::LIST_SHOW_COUNT]);
                $modelArr = $model->groupBy(array('main.id'))->offset($pages->offset)->limit($pages->limit)->all();

                return $this->render("newinformation", array(
                    'params' => $params,
                    'modelArr' => $modelArr,
                    'pages' => $pages,
                    'totalCount' => $totalCount,
                    'isSenior' => $this->getIsSenior(),
                    'qualificationArr' => $this->getQualification(),
                    'level' => $level
                ));
            }
        } else {
            return $this->redirect(Yii::$app->params['home_url']);
        }

    }

    /**
     * render library credit list and search page
     * @return string
     */
    public function actionCredit()
    {
        // 协议认定参数
        $parameter = Yii::$app->request->get('deal');
        isset($parameter) ? $deal=$parameter : $deal=0; // deal=1,同意协议; deal=0,不同意协议

        if ($deal == 1) {
            // 登录及权限验证
            //$jump_url = Url::to('/library/credit', true);
            //$jump_url = Url::to(['library/credit']); //本地
            //$this ->loginCheck($jump_url);
            $id = $this->loginuserinfo['id'];
            $login_name = $this->loginuserinfo['login_name'];

            if($id == 3316 && $login_name == '成都乐氏') {
                $service = new BsSdServices();
                $data = new BdataServices();
                $get = Yii::$app->request->get();
                // get params
                $params = $this->getParamsByGet($get);
                // get model
                $model = $this->getCreditByParams($params);
                $totalCount = $model->count();
                $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => self::LIST_SHOW_COUNT]);
                $modelArr = $model->offset($pages->offset)->limit($pages->limit)->all();

                $industrys = $data->getDataByType(BdataServices::INDUSTRY_BTYPE_ID);
                // get industry array(id =>name)
                $industryArray = array();
                foreach ($industrys as $industry) {
                    $industryArray[$industry->id] = $industry->name;
                }

                return $this->render("credit", array(
                    'params' => $params,
                    'modelArr' => $modelArr,
                    'pages' => $pages,
                    'totalCount' => $totalCount,
                    'industryArray' => $industryArray,
                    'service' => $service,
                    'library' => $this,
                ));
            } else {
                $level = $this->loginuserinfo['level'];
                $get = Yii::$app->request->get();
                $params = $this->getParamsByGet($get);
                $model = $this->getCreditByParams($params);
                $totalCount = $model->count();
                $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => self::LIST_SHOW_COUNT]);
                $modelArr = $model->offset($pages->offset)->limit($pages->limit)->all();

                return $this->render("newcredit", array(
                    'params' => $params,
                    'modelArr' => $modelArr,
                    'pages' => $pages,
                    'totalCount' => $totalCount,
                    'library' => $this,
                    'level' => $level
                ));
            }
        } else {
            return $this->redirect(Yii::$app->params['home_url']);
        }

    }

    /**
     * render company senior detail page
     * @return string
     */
    public function actionDetail()
    {
        if (!$this->getIsSenior()) {
            return $this->redirect(Yii::$app->params['home_url']);
        }

        $id = $this->loginuserinfo['id'];
        $login_name = $this->loginuserinfo['login_name'];
        if($id == 3316 && $login_name == '成都乐氏') {
            $data = new BdataServices;
            $get = Yii::$app->request->get();
            $params = $this->getParamsByGet($get);
            // get company model
            $referrerUrl = Yii::$app->request->getReferrer();

            if (empty($params['id'])) {
                return $this->redirect($referrerUrl);
            }

            $model = $this->getCompanyByParams($params)->one();

            if (is_null($model)) {
                return $this->redirect($referrerUrl);
            }

            return $this->render("detail", array(
                'model' => $model,
                'library' => $this,
                'data' => $data,
                'referrerUrl' => $referrerUrl,
            ));
        } else {
            echo "<script>
                    alert('对不起！您没有访问该页面的权限。'); 
                    location.href='/';
                  </script>";
        }

    }

    /**
     * render level
     * @return string
     */
    public function actionLevel()
    {
        $this->layout = false;
        return $this->render("level");
    }

    /**
     * get company qualification
     * @return array
     */
    public function getQualification()
    {
        $qualificationArr = array(
            'qu1' => '国家高新技术企业',
            'qu2' => '技术先进型服务企业',
            'qu3' => '软件企业认定',
            'qu4' => '上规入库企业',
            'qu5' => '上市企业',
            'qu6' => '全国中小企业股份转让系统挂牌',
            'qu7' => '成都(川藏)股权交易中心(融资板或交易板)挂牌',
            'qu8' => '完成股份制改造',
        );
        return $qualificationArr;
    }

    /**
     * get credit level
     * @return array
     */
    public function getLevel()
    {
        $levelArr = array(
            '1' => '一星',
            '2' => '二星',
            '3' => '三星',
            '4' => '四星',
            '5' => '五星',
        );
        return $levelArr;
    }

    /**
     * get company model by params
     * @param array $params
     * @return $this
     */
    public function getCompanyByParams($params = array())
    {
        $data = (new \yii\db\Query())->from('bs_senior_company as main')
            ->orderBy('main.info_sort DESC');
        // get model by id
        if (!empty($params['id'])) {
            //$data->where(array('main.status' => 2, 'main.id' => $params['id']));
            $data->where(array('main.id' => $params['id']));
            return $data;
        }

        // get model by params
        $data->select(array('main.id', 'name', 'reg_capital', 'reg_date', 'link_name', 'legal_rep' , 'incubator_id', 'work_address', 'code' , 'link_tel','member_id'))
            ->where('main.status < 4');

        if (!empty($params['li'])) {
            $data->andWhere(array('like', "concat_ws(',',main.name,main.work_address,main.legal_rep)", $params['li']));
        }
        if (!empty($params['qu'])) {
            $qu = implode(',', $params['qu']);
            $data->andWhere(array('like', "concat_ws(','," . $qu . ")", 2));
        }
        if (!empty($params['pr'])) {
            $compare = $this->getCompare($params['co']);
            $data->leftJoin('bs_senior_sales as sales', 'main.id = sales.senior_id and main.member_id = sales.member_id')
                ->andWhere(array('sales.year' => $params['ye']))
                ->andWhere(array($compare, 'sales.income', $params['pr']));
        }
        if (!empty($params['in'])) {
            $data->leftJoin('bs_senior_industry as indu', 'main.id = indu.senior_id and main.member_id = indu.member_id')
                ->andWhere(array('indu.industry_id' => $params['in']));
        }
        return $data;
    }

    /**
     * get company model by params
     * @param array $params
     * @return $this
     */
    public function getCreditByParams($params = array())
    {
        $data = (new \yii\db\Query())
            ->select(array('main.id', 'name', 'main.member_id', 'reg_capital', 'credit.level', 'incubator_id', 'work_address', 'code', 'legal_rep', 'link_tel'))
            ->from('bs_senior_company as main ')
            ->leftJoin('bs_credit as credit', 'main.member_id = credit.member_id ')
            ->where('main.status < 4')
            ->groupBy('main.id')
            ->orderBy('credit.level DESC, main.info_sort DESC');
        // if params
        if (!empty($params['le'])) {
            $data->andWhere(array('credit.level' => $params['le']));
        }
        if (!empty($params['li'])) {
            $data->andWhere(array('like', "concat_ws(',',main.name,main.work_address,main.legal_rep)", $params['li']));
        }
        if (!empty($params['qu'])) {
            $qu = implode(',', $params['qu']);
            $data->andWhere(array('like', "concat_ws(','," . $qu . ")", 2));
        }
        if (!empty($params['pr'])) {
            $compare = $this->getCompare($params['co']);
            $data->leftJoin('bs_senior_sales as sales', 'main.id = sales.senior_id and main.member_id = sales.member_id')
                ->andWhere(array('sales.year' => $params['ye']))
                ->andWhere(array($compare, 'sales.income', $params['pr']));
        }
        if (!empty($params['in'])) {
            $data->leftJoin('bs_senior_industry as indu', 'main.id = indu.senior_id and main.member_id = indu.member_id')
                ->andWhere(array('indu.industry_id' => $params['in']));
        }
        return $data;
    }

    /**
     * get params by get
     * @param $get
     * @return mixed
     */
    public function getParamsByGet($get)
    {
        $id = isset($get['id']) ? SerRoute::getParam($get['id']) : '';
        $like = isset($get['li']) ? $get['li'] : '';
        $year = isset($get['ye']) ? $get['ye'] : '';
        $price = isset($get['pr']) ? $get['pr'] : '';
        $industryId = isset($get['in']) ? $get['in'] : array();
        $compare = isset($get['co']) ? $get['co'] : '';
        $qualification = isset($get['qu']) ? $get['qu'] : array();
        $level = isset($get['le']) ? $get['le'] : array();
        foreach ($get as $k => $value) {
            if (is_array($value) && (empty($like) || empty($id) || empty($year) || empty($price) || empty($industryId) || empty($compare) || empty($qualification) || empty($level))) {
                $id = isset($value['id']) ? SerRoute::getParam($value['id']) : $id;
                $like = isset($value['li']) ? $value['li'] : $like;
                $year = isset($value['ye']) ? $value['ye'] : $year;
                $price = isset($value['pr']) ? $value['pr'] : $price;
                $industryId = isset($value['in']) ? $value['in'] : $industryId;
                $compare = isset($value['co']) ? $value['co'] : $compare;
                $qualification = isset($value['qu']) ? $value['qu'] : $qualification;
                $level = isset($value['le']) ? $value['le'] : $level;
            }
        }
        // get params array
        $params['id'] = $id;
        $params['li'] = $like;
        $params['ye'] = $year;
        $params['pr'] = $price;
        $params['in'] = $industryId;
        $params['co'] = $compare;
        $params['qu'] = $qualification;
        $params['le'] = $level;
        return $params;
    }

    /**
     * get compare
     * @param $compare
     * @return string
     */
    public function getCompare($compare)
    {
        switch ($compare) {
            case 1:
                return '=';
                break;
            case 2:
                return '>=';
                break;
            case 3:
                return '<=';
                break;
            default:
                return '=';
        }
    }

    /**
     * get company industry string by senior id and member id
     * @param $seniorId
     * @param $memberId
     * @param bool $other
     * @return string
     */
    public function getIndustryStrById($seniorId, $memberId, $other = false)
    {
        $industryStr = '';
        $data = (new \yii\db\Query())->select('data.name')
            ->from('bs_senior_industry as ind')
            ->leftJoin('bs_bdata as data', 'ind.industry_id = data.id')
            ->where(array('ind.member_id' => $memberId, 'ind.senior_id' => $seniorId))->all();
        foreach ($data as $item) {
            $industryStr .= $item['name'] . ',';
        }
        $industryStr = substr($industryStr, 0, -1);
        // get other
        if ($other) {
            $industryOther = (new \yii\db\Query())->select(array('note'))
                ->from('bs_senior_industry')
                ->where(array('member_id' => $memberId, 'senior_id' => $seniorId, 'industry_id' => BdataServices::INDUSTRY_OTHER_ID))
                ->one();
            if (!empty($industryOther)) {
                $note = $industryOther['note'];
                $industryStr = $industryStr . ':' . $note;
            }
        }
        return $industryStr;
    }

    /**
     * get company sales by member id and senior id
     * @param $seniorId
     * @param $memberId
     * @return array
     */
    public function getCompanySales($seniorId, $memberId)
    {
        $sales = (new \yii\db\Query())
            ->from('bs_senior_sales')
            ->where(array('member_id' => $memberId, 'senior_id' => $seniorId))
            ->orderBy('year ASC')
            ->all();
        return $sales;
    }

    /**
     * get company shareholder by member id and senior id
     * @param $seniorId
     * @param $memberId
     * @return array
     */
    public function getCompanyShareholder($seniorId, $memberId)
    {
        $shareholder = (new \yii\db\Query())
            ->from('bs_senior_shareholder')
            ->where(array('member_id' => $memberId, 'senior_id' => $seniorId))
            ->all();
        return $shareholder;
    }

    /**
     * @param $idStr
     * @param $valArr
     * @return string
     */
    public function getCheckBoxValue($idStr, $valArr)
    {
        $checkStr = '';
        $other = '';
        if (!empty($idStr)) {
            $idArr = json_decode($idStr, true);
            if (is_array($idArr)) {
                if (in_array('other', $idArr)) {
                    $other = ':' . $idArr['other'];
                    unset($idArr['other']);
                }
                foreach ($valArr as $id => $value) {
                    if (in_array($id, $idArr)) {
                        $checkStr .= $value . ',';
                    }
                }
            }
            $checkStr = substr($checkStr, 0, -1);
            $checkStr .= $other;
        }
        return $checkStr;
    }
}