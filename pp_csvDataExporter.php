<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Pp_csvDataExporter extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'pp_csvDataExporter';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'PrestaPro';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CSV data exporter');
        $this->description = $this->l('The module allows exporting data as CSV files for default PrestaShop CSV import method');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }


    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submit_categories_export')) == true) {
            $this->postCategoriesExport();
        }
        if (((bool)Tools::isSubmit('submit_products_export')) == true) {
            $this->postProductsExport();
        }
        if (((bool)Tools::isSubmit('submit_products_texts')) == true) {
            $this->submit_products_texts();
        }
        if (((bool)Tools::isSubmit('submit_products_prices')) == true) {
            $this->submit_products_prices();
        }
        if (((bool)Tools::isSubmit('submit_products_stock')) == true) {
            $this->submit_products_stock();
        }
        if (((bool)Tools::isSubmit('submit_products_attributes')) == true) {
            $this->submit_products_attributes();
        }
        if (((bool)Tools::isSubmit('submit_manufacturers')) == true) {
            $this->submit_manufacturers();
        }
        if (((bool)Tools::isSubmit('submit_addresses')) == true) {
            $this->submit_addresses();
        }


        $this->context->smarty->assign([
            'module_dir' => $this->_path,
            'form_url' => $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name,
            'token' => Tools::getAdminTokenLite('AdminModules')
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');


    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPp_csvDataExporterModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'input' => array(

                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'PP_CSVDATAEXPORTER_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Save form data.
     */
    protected function postCategoriesExport()
    {
        $id_lang = 1;
        $id_shop = 1;
        $sql = 'SELECT 
`' . _DB_PREFIX_ . 'category`.`id_category`,
`' . _DB_PREFIX_ . 'category`.active,
`' . _DB_PREFIX_ . 'category_lang`.name,
cat2.name AS "Parent category", 
IF(`' . _DB_PREFIX_ . 'category`.id_category = 1, 1, 0) AS "Root category (0/1)",
`' . _DB_PREFIX_ . 'category_lang`.description,
`' . _DB_PREFIX_ . 'category_lang`.meta_title,
`' . _DB_PREFIX_ . 'category_lang`.meta_keywords,
`' . _DB_PREFIX_ . 'category_lang`.meta_description,
`' . _DB_PREFIX_ . 'category_lang`.link_rewrite
FROM `' . _DB_PREFIX_ . 'category_lang`
JOIN `' . _DB_PREFIX_ . 'category` ON `' . _DB_PREFIX_ . 'category`.id_category = `' . _DB_PREFIX_ . 'category_lang`.id_category
LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` as cat2 ON cat2.id_category = `' . _DB_PREFIX_ . 'category`.id_parent AND cat2.id_lang = '. $id_lang .' AND cat2.id_shop = '. $id_shop .'
WHERE `' . _DB_PREFIX_ . 'category_lang`.id_lang ='. $id_lang . ' and `' . _DB_PREFIX_ . 'category_lang`.id_shop ='. $id_shop;


        return $this->arrayToCsv(Db::getInstance()->executeS($sql), "categories");
    }

    protected function postProductsExport()
    {
        $id_lang = 1;
        $sql = 'SELECT `' . _DB_PREFIX_ . 'product`.`id_product`,
       `' . _DB_PREFIX_ . 'product`.`active`,
       `pl`.`name`,
       (
           SELECT GROUP_CONCAT(`cl`.`name`)
           FROM `' . _DB_PREFIX_ . 'category_product`
                    JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON `cl`.`id_category` = `' . _DB_PREFIX_ . 'category_product`.`id_category`
           WHERE `' . _DB_PREFIX_ . 'category_product`.`id_product` = `' . _DB_PREFIX_ . 'product`.`id_product`
             and `cl`.`id_lang` = ' . $id_lang . '
       )                                 as "Categories (x,y,z...)",
       `' . _DB_PREFIX_ . 'product`.`price`              as "Patikslinti kaina",
       `' . _DB_PREFIX_ . 'product`.`id_tax_rules_group` as "patikslinti tax",
       `' . _DB_PREFIX_ . 'product`.`wholesale_price`,
       `' . _DB_PREFIX_ . 'product`.`on_sale`,
       NULL                              as "discount amount",
       NULL                              as "discount Percent",
       `sp`.from                         as "discount from",
       `sp`.to                           as "discount to",
       `' . _DB_PREFIX_ . 'product`.`reference`,
       `' . _DB_PREFIX_ . 'product`.`supplier_reference`,
       `' . _DB_PREFIX_ . 'product`.`id_supplier`,
       `' . _DB_PREFIX_ . 'product`.`id_manufacturer`,
       `' . _DB_PREFIX_ . 'product`.`ean13`,
       `' . _DB_PREFIX_ . 'product`.`upc`,
       `' . _DB_PREFIX_ . 'product`.`ecotax`,
       `' . _DB_PREFIX_ . 'product`.`width`,
       `' . _DB_PREFIX_ . 'product`.`height`,
       `' . _DB_PREFIX_ . 'product`.`depth`,
       `' . _DB_PREFIX_ . 'product`.`weight`,
       `' . _DB_PREFIX_ . 'product`.`quantity`,
       `' . _DB_PREFIX_ . 'product`.`minimal_quantity`,
       `' . _DB_PREFIX_ . 'product`.`low_stock_threshold`,
       `' . _DB_PREFIX_ . 'product`.`visibility`,
       `' . _DB_PREFIX_ . 'product`.`additional_shipping_cost`,
       `' . _DB_PREFIX_ . 'product`.`unity`,
       `' . _DB_PREFIX_ . 'product`.`unit_price_ratio`,
       `pl`.`description_short`,
       `pl`.`description`,
       (
           SELECT GROUP_CONCAT(`t`.`name`)
           FROM `' . _DB_PREFIX_ . 'product_tag`
                    JOIN `' . _DB_PREFIX_ . 'tag` t ON `t`.`id_tag` = `' . _DB_PREFIX_ . 'product_tag`.`id_tag`
           WHERE `' . _DB_PREFIX_ . 'product_tag`.`id_product` = `' . _DB_PREFIX_ . 'product`.`id_product`
             and `t`.`id_lang` = ' . $id_lang . '
       )                                 as "Tags (x,y,z...)",
       `pl`.`meta_title`,
       `pl`.`meta_keywords`,
       `pl`.`meta_description`,
       `pl`.`link_rewrite`               as "URL rewritten",
       `pl`.`delivery_in_stock`          as "Text when in stock",
       `pl`.`delivery_out_stock`         as "text when backorder allowed",
       `' . _DB_PREFIX_ . 'product`.`available_for_order`,
       `' . _DB_PREFIX_ . 'product`.`available_date`,
       `' . _DB_PREFIX_ . 'product`.`date_add`,
       `' . _DB_PREFIX_ . 'product`.`show_price`,
       (
           SELECT GROUP_CONCAT(`i`.`id_image`)
           FROM `' . _DB_PREFIX_ . 'image` as i
           WHERE `i`.`id_product` = `' . _DB_PREFIX_ . 'product`.`id_product`
           ORDER BY i.cover DESC, i.position ASC
       )                                 as "Image URLs (x,y,z...)",
       (
           SELECT GROUP_CONCAT(`il`.`legend`)
           FROM `' . _DB_PREFIX_ . 'image` as i
                    JOIN `' . _DB_PREFIX_ . 'image_lang` il on `il`.`id_image` = `i`.`id_image`
           WHERE `i`.`id_product` = `' . _DB_PREFIX_ . 'product`.`id_product`
           ORDER BY i.cover DESC, i.position ASC
       )                                 as "Image alt text (x,y,z...)",
       0                                 as "Delete existing images (0 = No, 1 = Yes)",
       (
           SELECT GROUP_CONCAT(`fp`.`id_feature`, `fvl`.`value`)
           FROM `' . _DB_PREFIX_ . 'feature_product` as fp
                    JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON `fvl`.`id_feature_value` = `fp`.`id_feature_value`
           WHERE `fp`.`id_product` = `' . _DB_PREFIX_ . 'product`.`id_product`
       )                                 as "Feature(Name:Value:Position)",
       `' . _DB_PREFIX_ . 'product`.`online_only`,
       `' . _DB_PREFIX_ . 'product`.`condition`,
       `' . _DB_PREFIX_ . 'product`.`customizable`,
       `' . _DB_PREFIX_ . 'product`.`uploadable_files`,
       `' . _DB_PREFIX_ . 'product`.`text_fields`,
       `' . _DB_PREFIX_ . 'product`.`out_of_stock`,
       `ps`.`id_shop`                    as "ID / Name of Shop",
       `' . _DB_PREFIX_ . 'product`.`advanced_stock_management`,
       NULL                              as "Depends On Stock",
       NULL                              as "Warehouse"
FROM `' . _DB_PREFIX_ . 'product`
         JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON `pl`.`id_product` = `' . _DB_PREFIX_ . 'product`.`id_product`
         JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON `ps`.`id_product` = `' . _DB_PREFIX_ . 'product`.`id_product`
         LEFT JOIN `' . _DB_PREFIX_ . 'specific_price` sp ON `sp`.`id_product` = `' . _DB_PREFIX_ . 'product`.`id_product`
WHERE `pl`.id_lang = ' . $id_lang;
        return $this->arrayToCsv(Db::getInstance()->executeS($sql), "products");
    }

    protected function submit_products_texts()
    {
        $id_lang = 1;
        $sql = '
        SELECT 
            `id_product`, `name`, `description`, `description_short`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`, `available_now`, `available_later`, `delivery_in_stock`, `delivery_out_stock`
        FROM `' . _DB_PREFIX_ . 'product_lang` 
        WHERE `id_lang` = ' . $id_lang;
        return $this->arrayToCsv(Db::getInstance()->executeS($sql), "product_texts");
    }

    protected function submit_products_prices()
    {
        $sql = '
        SELECT 
            `id_product`, `price`, `wholesale_price`, `unity`, `unit_price_ratio`, `additional_shipping_cost`, `available_for_order`, `show_price`
        FROM `' . _DB_PREFIX_ . 'product`';
        return $this->arrayToCsv(Db::getInstance()->executeS($sql), "product_prices");
    }

    private function submit_products_stock()
    {
        $sql = '
        SELECT 
            `id_product`, `id_product_attribute`, `quantity`, `depends_on_stock`, `out_of_stock`
        FROM `' . _DB_PREFIX_ . 'stock_available`';
        return $this->arrayToCsv(Db::getInstance()->executeS($sql), "product_stock");
    }

    private function submit_products_attributes()
    {
        $id_lang = 1;
        $sql = '
        SELECT 
            `id_product`, `id_product_attribute`, `quantity`, `depends_on_stock`, `out_of_stock`
        FROM `' . _DB_PREFIX_ . 'stock_available`';
        return $this->arrayToCsv(Db::getInstance()->executeS($sql), "product_attributes");
    }

    private function submit_manufacturers()
    {
        $id_lang = 1;
        $sql ='SELECT 
`' . _DB_PREFIX_ . 'manufacturer`.`id_manufacturer`, 
`' . _DB_PREFIX_ . 'manufacturer`.active, 
`' . _DB_PREFIX_ . 'manufacturer`.name, 
`' . _DB_PREFIX_ . 'manufacturer_lang`.description, 
`' . _DB_PREFIX_ . 'manufacturer_lang`.meta_title, 
`' . _DB_PREFIX_ . 'manufacturer_lang`.meta_keywords, 
`' . _DB_PREFIX_ . 'manufacturer_lang`.meta_description
FROM `' . _DB_PREFIX_ . 'manufacturer`
JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ON `' . _DB_PREFIX_ . 'manufacturer`.id_manufacturer = `' . _DB_PREFIX_ . 'manufacturer_lang`.id_manufacturer
WHERE `'._DB_PREFIX_ .'manufacturer_lang`.id_lang = '.$id_lang;

        return $this->arrayToCsv(Db::getInstance()->executeS($sql), "manufacturers");
    }

    private function submit_addresses()
    {

    }

    private function arrayToCsv($array, $dataType)
    {

        $result = [];
        foreach ($array as $rowNo => $row) {
            foreach ($row as $key => $value) {
                if ($rowNo == 0) {
                    $result[$rowNo][] = $key;
                }
                if ($key == "Image URLs (x,y,z...)" && !empty($value)) {
                    $images = explode(',', $value);
                    $value = [];
                    foreach ($images as $image) {
                        $value[] = Configuration::get('`' . _DB_PREFIX_ . 'SHOP_DOMAIN') . '/img/p/' . implode('/', str_split($image)) . '/' . $image . '.jpg';
                    }
                    $value = implode(",", $value);
                }
                $result[$rowNo + 1][] = $value !== NULL ? $value : '';
            }
        }

        $this->array_to_csv_download($result, $dataType);
    }

    private function array_to_csv_download($array, $filename = "export.csv", $delimiter = ";")
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls";');
        $f = fopen('php://output', 'w+');
        // loop over the input array
        fputs($f, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        foreach ($array as $line) {
            // generate csv lines from the inner arrays
            fputcsv($f, $line, $delimiter);
        }
        fclose($f);
        exit;
    }

}
