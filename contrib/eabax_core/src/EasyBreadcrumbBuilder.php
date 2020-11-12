<?php

namespace Drupal\eabax_core;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\easy_breadcrumb\EasyBreadcrumbBuilder as EasyBreadcrumbBuilderBase;
use Symfony\Component\HttpFoundation\Request;

class EasyBreadcrumbBuilder extends EasyBreadcrumbBuilderBase {
  
  public function getTitleString(Request $route_request, RouteMatchInterface $route_match, array $replacedTitles) {
    $title = parent::getTitleString($route_request, $route_match, $replacedTitles);
    
    // 清除 HTML 标记，防止 breadcrumb 出现 HTML 代码.
    $title = Xss::filter($title, []);
    
    return $title;
  }
  
}