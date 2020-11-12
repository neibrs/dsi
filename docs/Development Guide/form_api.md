Form(表单)
==========

本文描述如何生成和操作表单及处理表单提交。

## 创建form

Form是实现`\Drupal\Core\Form\FormInterface`的类，并通过`\Drupal\Core\Form\FormBuilder`类构建。
平台提供了一些基类，可作为大多数基本表单的扩展起点，最常用的是`\Drupal\Core\Form\FormBase`。
FormBuilder对表单进行基础级处理，例如呈现必要的HTML，传入POST数据的初始处理，以及委托FormInterface的实现来验证和处理提交的数据。
下面是一个form类的事例:
```
namespace Drupal\mymodule\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
class ExampleForm extends FormBase {
  public function getFormId() {

    // Unique ID of the form.
    return 'example_form';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Create a $form API array.
    $form['phone_number'] = array(
      '#type' => 'tel',
      '#title' => $this
        ->t('Your phone number'),
    );
    $form['save'] = array(
      '#type' => 'submit',
      '#value' => $this
        ->t('Save'),
    );
    
    return $form;
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Validate submitted form data.
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Handle submitted form data.
  }

}
```

## 检索和显示form

平台通过`\Drupal::formBuilder（）->getForm（）`用于检索，处理和表单显示的HTML呈现。 
如上面定义的ExampleForm，`\Drupal::formBuilder（）->getForm（'Drupal\mymodule\Form\ExampleForm'）`
将返回由`ExampleForm::buildForm（）`定义的表单的HTML呈现，或调用validateForm（）和submitForm（），这取决于当前的处理状态。

其中`\Drupal::formBuilder（）->getForm（'xxx'）`的参数xxx是实现FormInterface的form类的名称。 
传递给getForm（）方法的任何其他参数将作为`ExampleForm::buildForm（）`方法的附加参数.
如下例，其中的$extra即为附加参数
```
$extra = '612-123-4567';
$form = \Drupal::formBuilder()->getForm('Drupal\mymodule\Form\ExampleForm', $extra);
...
public function buildForm(array $form, FormStateInterface $form_state, $extra = NULL)
  $form['phone_number'] = array(
    '#type' => 'tel',
    '#title' => $this->t('Your phone number'),
    '#value' => $extra,
  );
  return $form;
}
```
或者，可以通过路由系统即通过路由配置直接构建表单，该系统将负责调用`\Drupal::formBuilder（）->getForm（）`。 
以下示例演示如何在routing.yml文件里配置给定路径上需显示的表单。
```
example.form:
  path: '/example-form'
  defaults:
    _title: 'Example form'
    _form: '\Drupal\mymodule\Form\ExampleForm'
```    
与表单相关的函数的$form参数是一个包含表单元素和属性的专用渲染数组。 有关渲染数组的更多信息，请参阅[渲染API主题](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Render!theme.api.php/group/theme_render/8.6.x)。 
有关Form API工作流程的更详细说明，请参阅[Form API文档](https://www.drupal.org/node/2117411)。 此外，[Examples for Developers项目](https://www.drupal.org/project/examples)中还有一组Form API教程。

在表单构建，验证，提交和其他表单方法中，$form_state是对表单处理的主要影响对象，并传递给大多数方法，
因此他们可以使用$form_state与表单系统和彼此进行交互数据。 $form_state是一个实现`\Drupal\Core\Form\FormStateInterface`的对象。


