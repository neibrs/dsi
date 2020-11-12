<?php

namespace Drupal\person;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\lookup\Entity\LookupInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstallHelper implements ContainerInjectionInterface {

  /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager */
  protected $entityTypeManager;

  /**
   * InstallHelper constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function installData() {
    // GB_T2261_1 人的性别代码
    $lookup_storage = $this->entityTypeManager->getStorage('lookup');
    $data = [
      '1' => '男性',
      '2' => '女性',
      '9' => '未说明的性别',
      '0' => '未知的性别',
    ];
    foreach ($data as $key => $value) {
      $lookup_storage->create([
        'type' => 'gender',
        'name' => $value,
        'code' => $key,
      ])->save();
    }

    // GB_T2261_2 婚姻状况代码
    $data = [
      '10' => '未婚',
      '20' => '已婚',
      '21' =>	'初婚',
      '22' =>	'再婚',
      '23' =>	'复婚',
      '30' =>	'丧偶',
      '40' =>	'离婚',
      '90' =>	'未说明的婚姻状况',
    ];
    foreach ($data as $key => $value) {
      $lookup_storage->create([
        'type' => 'marital_status',
        'name' => $value,
        'code' => $key,
      ])->save();
    }

    // GB_T3304 民族类别代码
    $data = [
      '1' => '汉族',
      '2' => '蒙古族',
      '3' => '回族',
      '4' => '藏族',
      '5' => '维吾尔族',
      '6' => '苗族',
      '7' => '彝族',
      '8' => '壮族',
      '9' => '布依族',
      '10' => '朝鲜族',
      '11' => '满族',
      '12' => '侗族',
      '13' => '瑶族',
      '14' => '白族',
      '15' => '土家族',
      '16' => '哈尼族',
      '17' => '哈萨克族',
      '18' => '傣族',
      '19' => '黎族',
      '20' => '傈僳族',
      '21' => '佤族',
      '22' => '畲族',
      '23' => '高山族',
      '24' => '拉祜族',
      '25' => '水族',
      '26' => '东乡族',
      '27' => '纳西族',
      '28' => '景颇族',
      '29' => '柯尔克孜族',
      '30' => '土族',
      '31' => '达斡尔族',
      '32' => '仫佬族',
      '33' => '羌族',
      '34' => '布朗族',
      '35' => '撒拉族',
      '36' => '毛难族',
      '37' => '仡佬族',
      '38' => '锡伯族',
      '39' => '阿昌族',
      '40' => '普米族',
      '41' => '塔吉克族',
      '42' => '怒族',
      '43' => '乌孜别克族',
      '44' => '俄罗斯族',
      '45' => '鄂温克族',
      '46' => '德昂族',
      '47' => '保安族',
      '48' => '裕固族',
      '49' => '京族',
      '50' => '塔塔尔族',
      '51' => '独龙族',
      '52' => '鄂伦春族',
      '53' => '赫哲族',
      '54' => '门巴族',
      '55' => '珞巴族',
      '56' => '基诺族',
      '99' => '外国籍',
    ];
    foreach ($data as $key => $value) {
      $lookup_storage->create([
        'type' => 'nationality',
        'name' => $value,
        'code' => $key,
      ])->save();
    }

    // 卫生信息数据元值域代码: CV02_01_101 身份证件类别代码
    $data = [
      '01' => '居民身份证',
      '02' => '居民户口簿',
      '03' => '护照',
      '04' => '军官证',
      '05' => '驾驶证',
      '06' => '港澳居民往来内地通行证',
      '07' => '台湾居民往来内地通行证',
      '99' => '其他法定有效证件',
    ];
    foreach ($data as $key => $value) {
      $lookup_storage->create([
        'type' => 'identification_information_type',
        'name' => $value,
        'code' => $key,
      ])->save();
    }

    // CC99_01_001 联系方式类别代码
    $data = [
      '01' => '本人电话',
      '02' => '配偶电话',
      '03' => '监护人电话',
      '04' => '家庭电话',
      '05' => '本人工作单位电话',
      '06' => '居委会电话',
    ];
    foreach ($data as $key => $value) {
      $lookup_storage->create([
        'type' => 'person_phone_type',
        'name' => $value,
        'code' => $key,
      ])->save();
    }

    // 卫生信息数据元值域代码: CV02_01_205地址类别代码
    $data = [
      '01' => '户籍住址',
      '02' => '工作场所地址',
      '03' => '家庭常住住址',
      '04' => '通讯地址',
      '05' => '暂住地址',
      '06' => '出生地址',
      '07' => '产后修养地址',
      '08' => '变迁地址',
      '09' => '现住址',
      '99' => '其他地址',
    ];
    foreach ($data as $key => $value) {
      $lookup_storage->create([
        'type' => 'person_address_type',
        'name' => $value,
        'code' => $key,
      ])->save();
    }

    // 邮箱类型
    $data = [
      '01' => '工作邮箱',
      '02' => '非工作邮箱',
    ];
    foreach ($data as $key => $value) {
      $lookup_storage->create([
        'type' => 'person_email_type',
        'name' => $value,
        'code' => $key,
      ])->save();
    }

  }

}
