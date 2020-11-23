<?php


namespace Drupal\dsi_finance;


use DrupalCodeGenerator\Command\Drupal_8\Controller;

class FinanceOverviewController extends Controller
{

    public function overview(){
        $database = \Drupal::database();
        $finance = $database->query("select id,name,receivable_price,received_price,wait_price from dsi_finance_field_data")->fetchAll();
        $total['receivable_price'] = 0;//总应收
        $total['received_price'] = 0;//总已收
        $total['wait_price'] = 0;//总待收
        $financeIds = [];
        $profitData = [];//收益分布 => 比例
        foreach ($finance as $key => $val){
            $total['receivable_price'] += $val->receivable_price;
            $total['received_price'] += $val->received_price;
            $total['wait_price'] += $val->wait_price;
            $financeIds[] = $val->id;
            if (!isset($profitData[$val->name])){
                $profitData[$val->name] = $val->receivable_price;
            }else{
                $profitData[$val->name] += $val->receivable_price;
            }
        }
        //收款记录
        $financeIds = implode(',',$financeIds);
        $detaileds = $database->query("select finance_id,price,invoice_price from dsi_finance_detailed_field_data where finance_id in ($financeIds)")->fetchAll();
        $total['invoice_price'] = 0;//开票金额
        foreach ($detaileds as $detailed => $val) {
            $total['invoice_price'] += $val->invoice_price;
        }

        //支出
        $expenditures = $database->query("select id,price,`name`,undertaker from dsi_finance_expenditure_field_data")->fetchAll();
        $total['expenditure_price'] = 0;//总支出
        $total['client_expenditure_price'] = 0;//客户支出
        $total['reimbursement_price'] = 0;//已报销
        $lookup_storage = \Drupal::service('entity_type.manager')->getStorage('lookup');
        $expenditureData = $costData = []; // 支出分布 => 比例  成本分布 => 比例
        foreach ($expenditures as $expenditure => $val){
            $total['expenditure_price'] += $val->price;
            $undertaker = $lookup_storage->load($val->undertaker);
            if ($undertaker->label() == '客户'){
                $total['client_expenditure_price'] += $val->price;
            }
            $reimbursement = $lookup_storage->load($val->reimbursement_status);
            if ($reimbursement->label() == '待报销'){
                $total['reimbursement_price'] += $val->price;
            }
            if (!isset($expenditureData[$val->name])){
                $expenditureData[$val->name] = $val->price;
            }else{
                $expenditureData[$val->name] += $val->price;
            }
            if (!isset($costData[$val->undertaker])){
                $costData[$val->undertaker] = $val->price;
            }else{
                $costData[$val->undertaker] += $val->price;
            }
        }
        $expenditure = $this->changeData($expenditureData,$total['expenditure_price']);
        $cost = $this->changeData($costData,$total['expenditure_price']);
        $profit = $this->changeData($profitData,$total['expenditure_price']);
        $total['actual_price'] = $total['expenditure_price'] - $total['client_expenditure_price'];//实际成本
        $total['service_profit'] = $total['receivable_price'] - $total['actual_price'];//服务收益
        $total['entry_profit'] = $total['received_price'] - $total['expenditure_price'] + $total['actual_price'];//服务收益
        return [
            'total'=>$total,
            'expenditure'=>$expenditure,
            'cost'=>$cost,
            'profit'=>$profit,
            ];
    }

    public function changeData($data,$price)
    {
        $expenditure = $costData = [];
        foreach ($data as $k => $v){
            $expenditure[] = [
                'name'=>$k,//名称
                'price'=>$v,//金额
                'rate'=>round($v/$price,2),//比例
            ];
        }
    }

}