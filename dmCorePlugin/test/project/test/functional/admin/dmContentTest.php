<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('admin');

$browser = $helper->getBrowser();

$helper->logout();

$browser->info('Posts list')
->get('/content?skip_browser_detection=1')
->checks(array(
  'module_action' => 'dmAdmin/moduleType',
  'code' => 401
))
->has('input.submit');

$helper->login();

$browser->info('Posts list')
->get('/content/blog/dm-test-posts/index')
->checks(array(
  'module_action' => 'dmTestPost/index'
))
->has('h1', 'Dm test posts')
->has('#breadCrumb')
->has('#dm_module_search_input')
->has('.sf_admin_list_td_title a.link')
->has('.dm_pagination_status', '1 - 10 of 20');

$browser->info('Loremize 20 posts')
->click('.dm_loremize a:contains(20)')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'loremize')
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', 'Dm test posts')
->checkElement('.dm_pagination_status', '1 - 10 of 20')
->checkElement('.flashs.infos', 'Successfully loremized')
->end();

$browser->info('Sort by created_at')
->click('.sf_admin_list_th_created_at a')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isParameter('sort', 'created_at')
->isParameter('sort_type', 'asc')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.sf_admin_list_th_created_at a.s16_sort_asc', 'Created at')
->end()
->click('.sf_admin_list_th_created_at a')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isParameter('sort', 'created_at')
->isParameter('sort_type', 'desc')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.sf_admin_list_th_created_at a.s16_sort_desc', 'Created at')
->end();

$browser->info('Sort by title ( i18n field )')
->click('.sf_admin_list_th_title a')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isParameter('sort', 'title')
->isParameter('sort_type', 'asc')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.sf_admin_list_th_title a.s16_sort_asc', 'Title')
->end()
->click('.sf_admin_list_th_title a')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isParameter('sort', 'title')
->isParameter('sort_type', 'desc')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.sf_admin_list_th_title a.s16_sort_desc', 'Title')
->end();

$browser->info('Sort by categ ( foreign + i18n field )')
->click('.sf_admin_list_th_categ_id a')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isParameter('sort', 'categ_id')
->isParameter('sort_type', 'asc')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.sf_admin_list_th_categ_id a.s16_sort_asc', 'Categ')
->end()
->click('.sf_admin_list_th_categ_id a')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isParameter('sort', 'categ_id')
->isParameter('sort_type', 'desc')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.sf_admin_list_th_categ_id a.s16_sort_desc', 'Categ')
->end();

$browser->info('Sort interface')
->click('.sf_admin_action a.dm_sort')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'sortTable')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', 'Sort table')
->checkElement('ol.objects li.object:first label', dmDb::query('DmTestPost p')->whereIsActive(true, 'DmTestPost')->orderBy('p.position ASC')->fetchOne()->title)
->end()
->click('input[type="submit"]')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'sortTable')
->isMethod('post')
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'sortTable')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', 'Sort table')
->checkElement('.flashs.infos', 'The items have been sorted successfully')
->end()
->click('« Back to list')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', 'Dm test posts')
->end();

$browser->info('Test batch actions')
->info('Delete')
->select('ids[]')
->click('input[name="batchDelete"]')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'batch')
->isMethod('post')
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.dm_pagination_status', '1 - 10 of 19')
->checkElement('.flashs.infos', 'The selected items have been deleted successfully.')
->end()
->info('Activate')
->select('ids[]')
->click('input[name="batchActivate"]')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'batch')
->isMethod('post')
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.sf_admin_list_td_is_active:first .s16_tick')
->checkElement('.flashs.infos', 'The selected items have been modified successfully')
->end()
->info('Deactivate')
->select('ids[]')
->click('input[name="batchDeactivate"]')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'batch')
->isMethod('post')
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.sf_admin_list_td_is_active:first .s16_cross')
->checkElement('.flashs.infos', 'The selected items have been modified successfully')
->end();

$browser->info('Create new post')
->click('a.sf_admin_action_new')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'new')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', 'New')
->end()
->info('Submit empty')
->click('Save')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'create')
->isMethod('post')
->end()
->with('response')->begin()
->isStatusCode(200)
->end()
->with('form')->begin()
->hasErrors(2)
->isError('title', 'required')
->isError('date', 'required')
->end()
->info('Submit with bad user_id, categ_id and date')
->click('Save', array('dm_test_post_admin_form' => array(
  'title' => dmString::random(),
  'user_id' => 9999,
  'categ_id' => 9999,
  'date' => 'bad date'
)))
->with('form')->begin()
->hasErrors(3)
->isError('user_id', 'invalid')
->isError('categ_id', 'invalid')
->isError('date', 'invalid')
->end()
->info('Submit valid')
->click('Save', array('dm_test_post_admin_form' => array(
  'title' => $postTitle = dmString::random(16),
  'user_id' => dmDb::table('DmUser')->findOne()->id,
  'categ_id' => dmDb::table('DmTestCateg')->findOne()->id,
  'date' => '01/13/2010',
  'excerpt' => 'resume 1'
)))
->with('form')->begin()
->hasErrors(0)
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'edit')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', $postTitle)
->checkElement('#dm_test_post_admin_form_excerpt', 'resume 1')
->end()
->info('Update post')
->click('Save', array('dm_test_post_admin_form' => array(
  'title' => $postTitle,
  'user_id' => dmDb::table('DmUser')->findOne()->id,
  'categ_id' => dmDb::table('DmTestCateg')->findOne()->id,
  'date' => '01/13/2012',
  'excerpt' => 'resume 2'
)))
->with('form')->begin()
->hasErrors(0)
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'edit')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', $postTitle)
->checkElement('#dm_test_post_admin_form_excerpt', 'resume 2')
->end();

$browser->info('Post history')
->click('History')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'history')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', 'Revision history')
->end()
->info('Revert to version 1')
->click('Revert to revision 1')
->with('request')->begin()
->isParameter('module', 'dmAdminGenerator')
->isParameter('action', 'revert')
->isParameter('model', 'DmTestPost')
->isParameter('version', 1)
->isMethod('get')
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'history')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', 'Revision history')
->end()
->info('Return to edit page')
->click('#breadCrumb a:last')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'edit')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', $postTitle)
->checkElement('#dm_test_post_admin_form_excerpt', 'resume 1')
->end()
->info('Return to list page')
->click('Back to list')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('h1', 'Dm test posts')
->checkElement('.dm_pagination_status', '1 - 10 of 20')
->end();

$browser->info('Search in posts')
->click('.dm_module_search input[type="submit"]', array('search' => $postTitle))
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isParameter('search', $postTitle)
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.dm_cancel_search')
->checkElement('.dm_pagination_status', '1 - 1 of 1')
//->checkElement('.sf_admin_list_td_title:first a', $postTitle)
//->checkElement('.sf_admin_list_td_title:last a', $postTitle)
->end()
->info('Cancel search')
->click('.dm_cancel_search')
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isParameter('search', null)
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.dm_pagination_status', '1 - 10 of 20')
->end();

$browser->info('Delete post')
->click('.sf_admin_list_td_title:first a')
->click('Delete', array(), array('method' => 'post'))
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'delete')
->isMethod('post')
->end()
->with('response')->begin()
->isRedirected()
->end()
->followRedirect()
->with('request')->begin()
->isParameter('module', 'dmTestPost')
->isParameter('action', 'index')
->isMethod('get')
->end()
->with('response')->begin()
->isStatusCode(200)
->checkElement('.dm_pagination_status', '1 - 10 of 19')
->end();