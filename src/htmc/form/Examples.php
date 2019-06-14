<?php
/***
 * 实例演示页面
 */
exit('QPF-Examples');

$htm = new Htmc();
$input = $htm->getInput();
$form = $htm->getForm()
    ->setAction('/admin/login')
    ->id('form1')
    ->name('form1');

// $form->setAction('/admin/login');
// $htm->head('htmc-test');

$htm->addClass(Css::this()->bg_color('#F3F3F3')
    ->createClass('user'));

$htm->htmCore[] = $form->tagStart();
// 文本框
$htm->htmCore[] = '服务器接收值: ' . $input->text('user', null, [
    'class' => 'user',
    'onClick' => "alert('ok');",
    'id' => 'text_user'

]);
// 密码
$htm->htmCore[] = '密码: ' . $input->password('pwd', null, [
    'class' => 'user'
]);
// 隐藏元素
$htm->htmCore[] = $input->hidden('hidden', 'QPF', [
    'form' => '111'
]);
// 上传
$htm->htmCore[] = $input->file('file', [
    'accept' => 'image/*'
]);

$htm->htmCore[] = $input->radio('sex', 'boy', [
    'checked' => true,
    'id' => 'sex_1'
]) . '男';
$htm->htmCore[] = $input->radio('sex', 'girl', [
    'id' => 'sex_2'
]) . '女';

// 原生下拉列表
$htm->htmCore[] = '<br> 选择： ' . Select::this()->name('type')
    ->classes('sss')
    ->size('4')
    ->add(0, '请选择')
    ->add(1, '选项1')
    ->add(2, '选项2', true)
    ->add(3, '选项3')
    ->onChange('alert(this.value)')
    ->getHtml();

// 封装下拉列表
/*
 * $htm->htmCore[] = '<br> 选择2： ' . $input->select('type2', [
 * ['a' => '苹果'],
 * ['b' => '香蕉', true],
 * ['c' => '菠萝'],
 * ]);
 * // 封装下拉列表
 * $htm->htmCore[] = '<br> 选择3： ' . $input->select('type3', [
 * ['key'=> 'a', 'value' => '苹果'],
 * ['key'=> 'b', 'value' => '香蕉', true],
 * ['key'=> 'c', 'value' => '菠萝'],
 * ]);
 */
// 封装下拉列表
/*
 * $htm->htmCore[] = '<br> 选择3： ' . $input->select('type3', [
 * 'a' => '苹果',
 * 'b' => '香蕉',
 * 'c' => '菠萝',
 * 'true' => 'b',
 * ]);
 */

// 提交
$htm->htmCore[] = $input->submit(null, null, [
    'class' => 'user',
    'onClick' => 'alert(\'输入的密码为：\' + document.form1.pwd.value);return false;'
]);

// 重置 - 无需name值，设置了也不会提交
$htm->htmCore[] = $input->reset('清空', [
    'name' => 'reset'
]);

$htm->htmCore[] = $form->tagEnd();

echo $htm->getHtml();
