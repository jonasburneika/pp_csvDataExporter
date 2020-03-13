{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<form id="module_form" class="defaultForm form-horizontal" action="{$form_url}&token={$token}" method="post"
	  enctype="multipart/form-data" novalidate="">
    <div class="panel">
        <div class="row">
            <div class="col-md-4">
				<button type="submit" id="submit_categories_export" name="submit_categories_export" class="btn btn-default pull-right">
					<i class="process-icon-save"></i>{l s='Export Categories' mod="pp_csvDataExporter"}</button>
			</div>
            <div class="col-md-4">
				<button type="submit" id="submit_products_export" name="submit_products_export" class="btn btn-default pull-right">
					<i class="process-icon-save"></i>{l s='Export Full Products data' mod="pp_csvDataExporter"}</button>
			</div>
            <div class="col-md-4">
				<button type="submit" id="submit_products_texts" name="submit_products_texts" class="btn btn-default pull-right">
					<i class="process-icon-save"></i>{l s='Export Product Texts' mod="pp_csvDataExporter"}</button>
			</div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <button type="submit" id="submit_products_prices" name="submit_products_prices" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i>{l s='Export Product Prices' mod="pp_csvDataExporter"}</button>
            </div>
            <div class="col-md-4">
                <button type="submit" id="submit_products_stock" name="submit_products_stock" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i>{l s='Export Product Stock' mod="pp_csvDataExporter"}</button>
            </div>
            <div class="col-md-4">
                <button type="submit" id="submit_products_attributes" name="submit_products_attributes" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i>{l s='Export Product Attributes' mod="pp_csvDataExporter"}</button>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <button type="submit" id="submit_manufacturers" name="submit_manufacturers" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i>{l s='Export Manufacturers' mod="pp_csvDataExporter"}</button>

                </div>
            <div class="col-md-4">alias_import</div>
            <div class="col-md-4">store_contacts</div>
        </div>
        <div class="row">
            <div class="col-md-4">suppliers_import</div>
            <div class="col-md-4">alias_import</div>
            <div class="col-md-4">store_contacts</div>
        </div>
    </div>
</form>