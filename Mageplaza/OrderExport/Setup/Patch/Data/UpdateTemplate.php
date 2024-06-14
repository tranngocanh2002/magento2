<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderExport
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
declare(strict_types=1);

namespace Mageplaza\OrderExport\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Mageplaza\OrderExport\Model\DefaultTemplateFactory;

/**
 * Class UpdateTemplate
 * @package Mageplaza\OrderExport\Setup\Patch\Data
 */
class UpdateTemplate implements
    DataPatchInterface,
    PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var DefaultTemplateFactory
     */
    private $defaultTemplate;

    /**
     * UpdateTemplate constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param DefaultTemplateFactory $defaultTemplate
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        DefaultTemplateFactory $defaultTemplate
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->defaultTemplate = $defaultTemplate;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $sampleTemplates   = [];
        $sampleTemplates[] = [
            'name'          => 'order_json',
            'title'         => 'Order JSON',
            'profile_type'  => 'order',
            'file_type'     => 'json',
            'template_html' => '{
    "orders": [
      {% for order in collection %}
      {
        	"increment_id": "{{ order.increment_id | escape }}",
        	"store_name": "{{ order.store_name | escape }}",
        	"created_at": "{{ order.created_at | escape }}",
        	"bill_to_name": "{{ order.billingAddress.firstname | escape }} {{ order.billingAddress.lastname | escape }}",
        	"ship_to_name": "{{ order.shippingAddress.firstname | escape }} {{ order.shippingAddress.lastname | escape }}",
        	"status": "{{ order.status }}",
        	"billing_address": "{{ order.billingAddress.street | escape }} {{ order.billingAddress.city | escape }} {{ order.billingAddress.postcode | escape }}",
        	"shipping_address": "{{ order.shippingAddress.street | escape }} {{ order.shippingAddress.city | escape }} {{ order.shippingAddress.postcode | escape }}",
        	"shipping_description": "{{ order.shipping_description | escape }}",
        	"customer_email": "{{ order.customer_email | escape }}",
        	"customer_group": "{{ order.customer_group | escape }}",
        	"base_shipping_incl_tax": "{{ order.base_shipping_incl_tax | escape }}",
        	"customer_name": "{{ order.customer_firstname | escape }} {{ order.customer_lastname | escape }}",
        	"payment_method": "{{ order.payment_method | escape }}",
          	"order_total": {
        		"subtotal": "{{ order.subtotal | escape }}",
        		"base_grand_total": "{{ order.base_grand_total | escape }}",
        		"grand_total": "{{ order.grand_total | escape }}",
        		"total_paid": "{{ order.total_paid | escape }}",
        		"total_refunded": "{{ order.total_refunded | escape }}",
        		"total_due": "{{ order.total_due | escape }}",
          	},
          	"items": [
              {% for item in order.sub_items %}
              {
        			"name": "{{ item.name | escape }}",
        			"sku": "{{ item.sku | escape }}",
        			"status": "{{ item.status | escape }}",
        			"original_price": "{{ item.original_price | escape }}",
        			"price": "{{ item.price | escape }}",
        			"qty_ordered": "{{ item.qty_ordered | escape }}",
        			"qty_invoiced": "{{ item.qty_invoiced | escape }}",
        			"qty_refunded": "{{ item.qty_refunded | escape }}",
        			"base_row_total_incl_tax": "{{ item.base_row_total_incl_tax | escape }}",
        			"tax_amount": "{{ item.tax_amount | escape }}",
        			"tax_percent": "{{ item.tax_percent | escape }}",
        			"discount_amount": "{{ item.discount_amount | escape }}",
        			"row_total": "{{ item.row_total | escape }}",
              },
      	  	  {% endfor %}
            ]
		},
      {% endfor %}
    ]
}',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];
        $sampleTemplates[] = [
            'name'          => 'order_xml',
            'title'         => 'Order XML',
            'profile_type'  => 'order',
            'file_type'     => 'xml',
            'template_html' => '<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>Sale Order</title>
          {% for order in collection %}
      		<order>
                <increment_id><![CDATA[{{ order.increment_id }}]]></increment_id>
                <store_name><![CDATA[{{ order.store_name }}]]></store_name>
                <created_at><![CDATA[{{ order.created_at }}]]></created_at>
                <bill_to_name><![CDATA[{{ order.billingAddress.firstname }} {{ order.billingAddress.lastname }}]]></bill_to_name>
                <ship_to_name><![CDATA[{{ order.shippingAddress.firstname }} {{ order.shippingAddress.lastname }}]]></ship_to_name>
                <status><![CDATA[{{ order.status }}]]></status>
                <billing_address><![CDATA[{{ order.billingAddress.street }} {{ order.billingAddress.city }} {{ order.billingAddress.postcode }}]]></billing_address>
                <shipping_address><![CDATA[{{ order.shippingAddress.street }} {{ order.shippingAddress.city }} {{ order.shippingAddress.postcode }}]]></shipping_address>
                <shipping_description><![CDATA[{{ order.shipping_description }}]]></shipping_description>
                <customer_email><![CDATA[{{ order.customer_email }}]]></customer_email>
                <customer_group><![CDATA[{{ order.customer_group }}]]></customer_group>
                <base_shipping_incl_tax><![CDATA[{{ order.base_shipping_incl_tax }}]]></base_shipping_incl_tax>
                <customer_name><![CDATA[{{ order.customer_firstname }} {{ order.customer_lastname }}]]></customer_name>
                <payment_method><![CDATA[{{ order.payment_method }}]]></payment_method>
                <order_total>
                	<subtotal><![CDATA[{{ order.subtotal }}]]></subtotal>
                    <base_grand_total><![CDATA[{{ order.base_grand_total }}]]></base_grand_total>
                    <grand_total><![CDATA[{{ order.grand_total }}]]></grand_total>
                    <total_paid><![CDATA[{{ order.total_paid }}]]></total_paid>
                    <total_refunded><![CDATA[{{ order.total_refunded }}]]></total_refunded>
                    <total_due><![CDATA[{{ order.total_due }}]]></total_due>
              	</order_total>
              	<items>
                  {% for item in order.sub_items %}
                    <item>
                      <name><![CDATA[{{ item.name }}]]></name>
                      <sku><![CDATA[{{ item.sku }}]]></sku>
                      <status><![CDATA[{{ item.status }}]]></status>
                      <original_price><![CDATA[{{ item.original_price }}]]></original_price>
                      <price><![CDATA[{{ item.price }}]]></price>
                      <qty_ordered><![CDATA[{{ item.qty_ordered }}]]></qty_ordered>
                      <qty_invoiced><![CDATA[{{ item.qty_invoiced }}]]></qty_invoiced>
                      <qty_refunded><![CDATA[{{ item.qty_refunded }}]]></qty_refunded>
                      <base_row_total_incl_tax><![CDATA[{{ item.base_row_total_incl_tax }}]]></base_row_total_incl_tax>
                      <tax_amount><![CDATA[{{ item.tax_amount }}]]></tax_amount>
                      <tax_percent><![CDATA[{{ item.tax_percent }}]]></tax_percent>
                      <discount_amount><![CDATA[{{ item.discount_amount }}]]></discount_amount>
                      <row_total><![CDATA[{{ item.row_total }}]]></row_total>
                    </item>
                  {% endfor %}
                </items>
      		</order>
          {% endfor %}
    </channel>
</rss>',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];
        $sampleTemplates[] = [
            'name'          => 'order_excel_xml',
            'title'         => 'Order Excel XML',
            'profile_type'  => 'order',
            'file_type'     => 'excel_xml',
            'template_html' => '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">
    <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"></OfficeDocumentSettings>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"></ExcelWorkbook>
    <Worksheet ss:Name="sales_order_grid.xml">
       	<Table>
            <Row>
                <Cell>
                    <Data ss:Type="String">ID</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Purchase Point</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Purchase Date</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Bill-to Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Ship-to Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Grand Total (Base)</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Grand Total (Purchased)</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Status</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Billing Address</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping Address</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping Information</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Email</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Group</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Subtotal</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping and Handling</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Payment Method</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Total Refunded</Data>
                </Cell>
            </Row>
          {% for order in collection %}
            <Row>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.increment_id }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.store_name }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.created_at }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.billingAddress.firstname }} {{ order.billingAddress.lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.shippingAddress.firstname }} {{ order.shippingAddress.lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.base_grand_total }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.grand_total }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.status }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.billingAddress.street }} {{ order.billingAddress.city }} {{ order.billingAddress.postcode }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.shippingAddress.street }} {{ order.shippingAddress.city }} {{ order.shippingAddress.postcode }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.shipping_description }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.customer_email }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.customer_group }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.subtotal }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.base_shipping_incl_tax }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.customer_firstname }} {{ order.customer_lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.payment_method }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ order.total_refunded }}]]></Data>
                </Cell>
            </Row>
          {% endfor %}
        </Table>
    </Worksheet>
</Workbook>',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];

        $sampleTemplates[] = [
            'name'           => 'order_csv',
            'title'          => 'Order CSV',
            'profile_type'   => 'order',
            'file_type'      => 'csv',
            'template_html'  => '',
            'fields_list'    => '{"1540889066862_862":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ order.increment_id }}"},"1540889098901_901":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ order.store_name }}"},"1540889115108_108":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ order.created_at }}"},"1540889124094_94":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ order.billingAddress.firstname }}"},"1540889186301_301":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ order.billingAddress.lastname }}"},"1540889210077_77":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ order.shippingAddress.firstname }}"},"1540889236859_859":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ order.shippingAddress.lastname }}"},"1540889250611_611":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ order.shipping_description }}"},"1540889295476_476":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ order.customer_email }}"},"1540889310449_449":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ order.customer_group }}"},"1540889718145_145":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ order.base_shipping_incl_tax }}"},"1540889756500_500":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ order.customer_firstname }}"},"1540889772716_716":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ order.customer_lastname }}"},"1540889814242_242":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ order.payment_method }}"},"1540889866084_84":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ order.subtotal }}"},"1540889882210_210":{"col_name":"Base Grand_total","col_type":"attribute","col_attr_val":"base_grand_total","col_pattern_val":"","col_val":"{{ order.base_grand_total }}"},"1540889896378_378":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ order.grand_total }}"},"1540889913109_109":{"col_name":"Total Paid","col_type":"attribute","col_attr_val":"total_paid","col_pattern_val":"","col_val":"{{ order.total_paid }}"},"1540889921269_269":{"col_name":"Subtotal Refunded","col_type":"attribute","col_attr_val":"subtotal_refunded","col_pattern_val":"","col_val":"{{ order.subtotal_refunded }}"},"1540889925884_884":{"col_name":"Total Due","col_type":"attribute","col_attr_val":"total_due","col_pattern_val":"","col_val":"{{ order.total_due }}"},"1540889967400_400":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540889974759_759":{"name":"Name","value":"name"},"1540889994942_942":{"name":"Sku","value":"sku"},"1540890143378_378":{"name":"Status","value":"status"},"1540890159410_410":{"name":"Original Price","value":"original_price"},"1540890193504_504":{"name":"Price","value":"price"},"1540890203385_385":{"name":"Qty Ordered","value":"qty_ordered"},"1540890216393_393":{"name":"Qty Invoiced","value":"qty_invoiced"},"1540890227420_420":{"name":"Qty Refunded","value":"qty_refunded"},"1540890262046_46":{"name":"Base Row Total Incl Tax","value":"base_row_total_incl_tax"},"1540890295074_74":{"name":"Tax Amount","value":"tax_amount"},"1540890301762_762":{"name":"Tax Percent","value":"tax_percent"},"1540890310967_967":{"name":"Discount Amount","value":"discount_amount"},"1540890319834_834":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];

        $sampleTemplates[] = [
            'name'           => 'order_tsv',
            'title'          => 'Order TSV',
            'profile_type'   => 'order',
            'file_type'      => 'tsv',
            'template_html'  => '',
            'fields_list'    => '{"1540889066862_862":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ order.increment_id }}"},"1540889098901_901":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ order.store_name }}"},"1540889115108_108":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ order.created_at }}"},"1540889124094_94":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ order.billingAddress.firstname }}"},"1540889186301_301":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ order.billingAddress.lastname }}"},"1540889210077_77":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ order.shippingAddress.firstname }}"},"1540889236859_859":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ order.shippingAddress.lastname }}"},"1540889250611_611":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ order.shipping_description }}"},"1540889295476_476":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ order.customer_email }}"},"1540889310449_449":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ order.customer_group }}"},"1540889718145_145":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ order.base_shipping_incl_tax }}"},"1540889756500_500":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ order.customer_firstname }}"},"1540889772716_716":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ order.customer_lastname }}"},"1540889814242_242":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ order.payment_method }}"},"1540889866084_84":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ order.subtotal }}"},"1540889882210_210":{"col_name":"Base Grand_total","col_type":"attribute","col_attr_val":"base_grand_total","col_pattern_val":"","col_val":"{{ order.base_grand_total }}"},"1540889896378_378":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ order.grand_total }}"},"1540889913109_109":{"col_name":"Total Paid","col_type":"attribute","col_attr_val":"total_paid","col_pattern_val":"","col_val":"{{ order.total_paid }}"},"1540889921269_269":{"col_name":"Subtotal Refunded","col_type":"attribute","col_attr_val":"subtotal_refunded","col_pattern_val":"","col_val":"{{ order.subtotal_refunded }}"},"1540889925884_884":{"col_name":"Total Due","col_type":"attribute","col_attr_val":"total_due","col_pattern_val":"","col_val":"{{ order.total_due }}"},"1540889967400_400":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540889974759_759":{"name":"Name","value":"name"},"1540889994942_942":{"name":"Sku","value":"sku"},"1540890143378_378":{"name":"Status","value":"status"},"1540890159410_410":{"name":"Original Price","value":"original_price"},"1540890193504_504":{"name":"Price","value":"price"},"1540890203385_385":{"name":"Qty Ordered","value":"qty_ordered"},"1540890216393_393":{"name":"Qty Invoiced","value":"qty_invoiced"},"1540890227420_420":{"name":"Qty Refunded","value":"qty_refunded"},"1540890262046_46":{"name":"Base Row Total Incl Tax","value":"base_row_total_incl_tax"},"1540890295074_74":{"name":"Tax Amount","value":"tax_amount"},"1540890301762_762":{"name":"Tax Percent","value":"tax_percent"},"1540890310967_967":{"name":"Discount Amount","value":"discount_amount"},"1540890319834_834":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'tab',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];

        $sampleTemplates[] = [
            'name'           => 'order_txt',
            'title'          => 'Order Txt',
            'profile_type'   => 'order',
            'file_type'      => 'txt',
            'template_html'  => '',
            'fields_list'    => '{"1540889066862_862":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ order.increment_id }}"},"1540889098901_901":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ order.store_name }}"},"1540889115108_108":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ order.created_at }}"},"1540889124094_94":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ order.billingAddress.firstname }}"},"1540889186301_301":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ order.billingAddress.lastname }}"},"1540889210077_77":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ order.shippingAddress.firstname }}"},"1540889236859_859":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ order.shippingAddress.lastname }}"},"1540889250611_611":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ order.shipping_description }}"},"1540889295476_476":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ order.customer_email }}"},"1540889310449_449":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ order.customer_group }}"},"1540889718145_145":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ order.base_shipping_incl_tax }}"},"1540889756500_500":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ order.customer_firstname }}"},"1540889772716_716":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ order.customer_lastname }}"},"1540889814242_242":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ order.payment_method }}"},"1540889866084_84":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ order.subtotal }}"},"1540889882210_210":{"col_name":"Base Grand_total","col_type":"attribute","col_attr_val":"base_grand_total","col_pattern_val":"","col_val":"{{ order.base_grand_total }}"},"1540889896378_378":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ order.grand_total }}"},"1540889913109_109":{"col_name":"Total Paid","col_type":"attribute","col_attr_val":"total_paid","col_pattern_val":"","col_val":"{{ order.total_paid }}"},"1540889921269_269":{"col_name":"Subtotal Refunded","col_type":"attribute","col_attr_val":"subtotal_refunded","col_pattern_val":"","col_val":"{{ order.subtotal_refunded }}"},"1540889925884_884":{"col_name":"Total Due","col_type":"attribute","col_attr_val":"total_due","col_pattern_val":"","col_val":"{{ order.total_due }}"},"1540889967400_400":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540889974759_759":{"name":"Name","value":"name"},"1540889994942_942":{"name":"Sku","value":"sku"},"1540890143378_378":{"name":"Status","value":"status"},"1540890159410_410":{"name":"Original Price","value":"original_price"},"1540890193504_504":{"name":"Price","value":"price"},"1540890203385_385":{"name":"Qty Ordered","value":"qty_ordered"},"1540890216393_393":{"name":"Qty Invoiced","value":"qty_invoiced"},"1540890227420_420":{"name":"Qty Refunded","value":"qty_refunded"},"1540890262046_46":{"name":"Base Row Total Incl Tax","value":"base_row_total_incl_tax"},"1540890295074_74":{"name":"Tax Amount","value":"tax_amount"},"1540890301762_762":{"name":"Tax Percent","value":"tax_percent"},"1540890310967_967":{"name":"Discount Amount","value":"discount_amount"},"1540890319834_834":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'          => 'invoice_json',
            'title'         => 'Invoice JSON',
            'profile_type'  => 'invoice',
            'file_type'     => 'json',
            'template_html' => '{
    "invoices": [
      {% for invoice in collection %}
      	{
        	"increment_id": "{{ invoice.increment_id | escape }}",
        	"order": "{{ invoice.order_id | escape }}",
        	"created_at": "{{ invoice.created_at | escape }}",
        	"order_date": "{{ invoice.order_date | escape }}",
        	"store_name": "{{ invoice.store_name | escape }}",
        	"customer_name": "{{ invoice.customer_firstname | escape }} {{ invoice.customer_lastname | escape }}",
        	"customer_email": "{{ invoice.customer_email | escape }}",
        	"customer_group": "{{ invoice.customer_group | escape }}",
          	"bill_to_name": "{{ invoice.billingAddress.firstname | escape }} {{ invoice.billingAddress.lastname | escape }}",
        	"billing_address": "{{ invoice.billingAddress.street | escape }} {{ invoice.billingAddress.city | escape }} {{ invoice.billingAddress.postcode | escape }}",
        	"billing_telephone": "{{ invoice.billingAddress.telephone }}",
          	"ship_to_name": "{{ invoice.shippingAddress.firstname | escape }} {{ invoice.shippingAddress.lastname | escape }}",
        	"shipping_address": "{{ invoice.shippingAddress.street | escape }} {{ invoice.shippingAddress.city | escape }} {{ invoice.shippingAddress.postcode | escape }}",
        	"shipping_telephone": "{{ invoice.shippingAddress.telephone }}",
        	"payment_method": "{{ invoice.payment_method | escape }}",
          	"shipping_description": "{{ invoice.shipping_description | escape }}",
        	"base_shipping_incl_tax": "{{ invoice.base_shipping_incl_tax | escape }}",
          	"order_total": {
        		"subtotal": "{{ invoice.subtotal | escape }}",
        		"base_shipping_incl_tax": "{{ invoice.base_shipping_incl_tax | escape }}",
        		"tax_amount": "{{ invoice.tax_amount | escape }}",
        		"grand_total": "{{ order.grand_total | escape }}",
          	},
          	"items": [
              {% for item in invoice.sub_items %}
              	{
        			"name": "{{ item.name | escape }}",
        			"sku": "{{ item.sku | escape }}",
        			"price": "{{ item.price | escape }}",
        			"qty": "{{ item.qty | escape }}",
        			"base_row_total": "{{ item.base_row_total | escape }}",
        			"tax_amount": "{{ item.tax_amount | escape }}",
        			"discount_amount": "{{ item.discount_amount | escape }}",
        			"row_total": "{{ item.row_total | escape }}",
              	},
      	  	  {% endfor %}
            ]
		},
      {% endfor %}
    ]
}',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];
        $sampleTemplates[] = [
            'name'          => 'invoice_xml',
            'title'         => 'Invoice XML',
            'profile_type'  => 'invoice',
            'file_type'     => 'xml',
            'template_html' => '<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>Sale Order Invoice</title>
          {% for invoice in collection %}
      		<invoice>
                <increment_id><![CDATA[{{ invoice.increment_id }}]]></increment_id>
                <order><![CDATA[{{ invoice.order_id }}]]></order>
                <created_at><![CDATA[{{ invoice.created_at }}]]></created_at>
                <order_date><![CDATA[{{ invoice.order_date }}]]></order_date>
                <store_name><![CDATA[{{ invoice.store_name }}]]></store_name>
                <customer_name><![CDATA[{{ invoice.customer_firstname }} {{ order.customer_lastname }}]]></customer_name>
                <customer_email><![CDATA[{{ invoice.customer_email }}]]></customer_email>
                <customer_group><![CDATA[{{ invoice.customer_group }}]]></customer_group>
                <bill_to_name><![CDATA[{{ invoice.billingAddress.firstname }} {{ invoice.billingAddress.lastname }}]]></bill_to_name>
                <billing_address><![CDATA[{{ invoice.billingAddress.street }} {{ invoice.billingAddress.city }} {{ invoice.billingAddress.postcode }}]]></billing_address>
                <billing_telephone><![CDATA[{{ invoice.billingAddress.telephone }}]]></billing_telephone>
                <ship_to_name><![CDATA[{{ invoice.shippingAddress.firstname }} {{ invoice.shippingAddress.lastname }}]]></ship_to_name>
                <shipping_address><![CDATA[{{ invoice.shippingAddress.street }} {{ invoice.shippingAddress.city }} {{ invoice.shippingAddress.postcode }}]]></shipping_address>
                <shipping_telephone><![CDATA[{{ invoice.shippingAddress.telephone }}]]></shipping_telephone>
                <payment_method><![CDATA[{{ invoice.payment_method }}]]></payment_method>
                <shipping_description><![CDATA[{{ invoice.shipping_description }}]]></shipping_description>
                <base_shipping_incl_tax><![CDATA[{{ invoice.base_shipping_incl_tax }}]]></base_shipping_incl_tax>
                <order_total>
                	<subtotal><![CDATA[{{ invoice.subtotal }}]]></subtotal>
                	<base_shipping_incl_tax><![CDATA[{{ invoice.base_shipping_incl_tax }}]]></base_shipping_incl_tax>
                    <tax_amount><![CDATA[{{ invoice.tax_amount }}]]></tax_amount>
                    <grand_total><![CDATA[{{ invoice.grand_total }}]]></grand_total>
              	</order_total>
              	<items>
                  {% for item in invoice.sub_items %}
                    <item>
                      <name><![CDATA[{{ item.name }}]]></name>
                      <sku><![CDATA[{{ item.sku }}]]></sku>
                      <price><![CDATA[{{ item.price }}]]></price>
                      <qty><![CDATA[{{ item.qty }}]]></qty>
                      <base_row_total><![CDATA[{{ item.base_row_total }}]]></base_row_total>
                      <tax_amount><![CDATA[{{ item.tax_amount }}]]></tax_amount>
                      <discount_amount><![CDATA[{{ item.discount_amount }}]]></discount_amount>
                      <row_total><![CDATA[{{ item.row_total }}]]></row_total>
                    </item>
                  {% endfor %}
                </items>
      		</invoice>
          {% endfor %}
    </channel>
</rss>',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];
        $sampleTemplates[] = [
            'name'          => 'invoice_excel_xml',
            'title'         => 'Invoice Excel XML',
            'profile_type'  => 'invoice',
            'file_type'     => 'excel_xml',
            'template_html' => '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">
    <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"></OfficeDocumentSettings>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"></ExcelWorkbook>
    <Worksheet ss:Name="sales_order_invoice_grid.xml">
       	<Table>
            <Row>
                <Cell>
                    <Data ss:Type="String">Invoice</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Invoice Date</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Order #</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Order Date</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Bill-to Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Status</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Grand Total (Base)</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Grand Total (Purchased)</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Purchased From</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Billing Address</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping Address</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Email</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Group</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Payment Method</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping Information</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Subtotal</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping and Handling</Data>
                </Cell>
            </Row>
          {% for invoice in collection %}
            <Row>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.increment_id }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.created_at }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.order_id }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.order_date }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.billingAddress.firstname }} {{ invoice.billingAddress.lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.state_name }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ invoice.base_grand_total }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ invoice.grand_total }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.store_name }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.billingAddress.street }} {{ invoice.billingAddress.city }} {{ invoice.billingAddress.postcode }} {{ invoice.billingAddress.telephone }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.shippingAddress.street }} {{ invoice.shippingAddress.city }} {{ invoice.shippingAddress.postcode }} {{ invoice.shippingAddress.telephone }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.customer_firstname }} {{ order.customer_lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.customer_email }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.customer_group }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.payment_method }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ invoice.shipping_description }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ invoice.subtotal }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ invoice.base_shipping_incl_tax }}]]></Data>
                </Cell>
            </Row>
          {% endfor %}
        </Table>
    </Worksheet>
</Workbook>',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];
        $sampleTemplates[] = [
            'name'           => 'invoice_csv',
            'title'          => 'Invoice Csv',
            'profile_type'   => 'invoice',
            'file_type'      => 'csv',
            'template_html'  => '',
            'fields_list'    => '{"1540893649080_80":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ invoice.increment_id }}"},"1540893657566_566":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ invoice.order_id }}"},"1540893671326_326":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ invoice.order_date }}"},"1540893754806_806":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540893762149_149":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ invoice.store_name }}"},"1540893777239_239":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540894096661_661":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ invoice.customer_firstname }}"},"1540894137596_596":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ invoice.customer_lasttname }}"},"1540894155805_805":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ invoice.customer_email }}"},"1540894234290_290":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ invoice.customer_group }}"},"1540894243101_101":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.firstname }}"},"1540894254635_635":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.lastname }}"},"1540894273292_292":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ invoice.billingAddress.street }}"},"1540894292410_410":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ invoice.billingAddress.city }}"},"1540894303282_282":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.billingAddress.postcode }}"},"1540894390760_760":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.billingAddress.telephone }}"},"1540894323146_146":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.firstname }}"},"1540894339586_586":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.lastname }}"},"1540894349301_301":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.street }}"},"1540894361930_930":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.city }}"},"1540894368163_163":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.postcode }}"},"1540894381689_689":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.telephone }}"},"1540894412449_449":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ invoice.payment_method }}"},"1540894471908_908":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ invoice.shipping_description }}"},"1540894698772_772":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ invoice.base_shipping_incl_tax }}"},"1540894727315_315":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ invoice.subtotal }}"},"1540894736608_608":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ invoice.tax_amount }}"},"1540894759127_127":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ invoice.grand_total }}"},"1540894764416_416":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540894769736_736":{"name":"Name","value":"name"},"1540894776343_343":{"name":"Sku","value":"sku"},"1540894788356_356":{"name":"Price","value":"price"},"1540894827811_811":{"name":"Qty","value":"qty"},"1540894838014_14":{"name":"Base Row_total","value":"base_row_total"},"1540894843724_724":{"name":"Tax Amount","value":"tax_amount"},"1540894850285_285":{"name":"Discount Amount","value":"discount_amount"},"1540894996810_810":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'invoice_txt',
            'title'          => 'Invoice Txt',
            'profile_type'   => 'invoice',
            'file_type'      => 'txt',
            'template_html'  => '',
            'fields_list'    => '{"1540893649080_80":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ invoice.increment_id }}"},"1540893657566_566":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ invoice.order_id }}"},"1540893671326_326":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ invoice.order_date }}"},"1540893754806_806":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540893762149_149":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ invoice.store_name }}"},"1540893777239_239":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540894096661_661":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ invoice.customer_firstname }}"},"1540894137596_596":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ invoice.customer_lasttname }}"},"1540894155805_805":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ invoice.customer_email }}"},"1540894234290_290":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ invoice.customer_group }}"},"1540894243101_101":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.firstname }}"},"1540894254635_635":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.lastname }}"},"1540894273292_292":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ invoice.billingAddress.street }}"},"1540894292410_410":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ invoice.billingAddress.city }}"},"1540894303282_282":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.billingAddress.postcode }}"},"1540894390760_760":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.billingAddress.telephone }}"},"1540894323146_146":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.firstname }}"},"1540894339586_586":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.lastname }}"},"1540894349301_301":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.street }}"},"1540894361930_930":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.city }}"},"1540894368163_163":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.postcode }}"},"1540894381689_689":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.telephone }}"},"1540894412449_449":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ invoice.payment_method }}"},"1540894471908_908":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ invoice.shipping_description }}"},"1540894698772_772":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ invoice.base_shipping_incl_tax }}"},"1540894727315_315":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ invoice.subtotal }}"},"1540894736608_608":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ invoice.tax_amount }}"},"1540894759127_127":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ invoice.grand_total }}"},"1540894764416_416":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540894769736_736":{"name":"Name","value":"name"},"1540894776343_343":{"name":"Sku","value":"sku"},"1540894788356_356":{"name":"Price","value":"price"},"1540894827811_811":{"name":"Qty","value":"qty"},"1540894838014_14":{"name":"Base Row_total","value":"base_row_total"},"1540894843724_724":{"name":"Tax Amount","value":"tax_amount"},"1540894850285_285":{"name":"Discount Amount","value":"discount_amount"},"1540894996810_810":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'invoice_tsv',
            'title'          => 'Invoice Tsv',
            'profile_type'   => 'invoice',
            'file_type'      => 'tsv',
            'template_html'  => '',
            'fields_list'    => '{"1540893649080_80":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ invoice.increment_id }}"},"1540893657566_566":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ invoice.order_id }}"},"1540893671326_326":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ invoice.order_date }}"},"1540893754806_806":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540893762149_149":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ invoice.store_name }}"},"1540893777239_239":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540894096661_661":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ invoice.customer_firstname }}"},"1540894137596_596":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ invoice.customer_lasttname }}"},"1540894155805_805":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ invoice.customer_email }}"},"1540894234290_290":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ invoice.customer_group }}"},"1540894243101_101":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.firstname }}"},"1540894254635_635":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.lastname }}"},"1540894273292_292":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ invoice.billingAddress.street }}"},"1540894292410_410":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ invoice.billingAddress.city }}"},"1540894303282_282":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.billingAddress.postcode }}"},"1540894390760_760":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.billingAddress.telephone }}"},"1540894323146_146":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.firstname }}"},"1540894339586_586":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.lastname }}"},"1540894349301_301":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.street }}"},"1540894361930_930":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.city }}"},"1540894368163_163":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.postcode }}"},"1540894381689_689":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.telephone }}"},"1540894412449_449":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ invoice.payment_method }}"},"1540894471908_908":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ invoice.shipping_description }}"},"1540894698772_772":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ invoice.base_shipping_incl_tax }}"},"1540894727315_315":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ invoice.subtotal }}"},"1540894736608_608":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ invoice.tax_amount }}"},"1540894759127_127":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ invoice.grand_total }}"},"1540894764416_416":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540894769736_736":{"name":"Name","value":"name"},"1540894776343_343":{"name":"Sku","value":"sku"},"1540894788356_356":{"name":"Price","value":"price"},"1540894827811_811":{"name":"Qty","value":"qty"},"1540894838014_14":{"name":"Base Row_total","value":"base_row_total"},"1540894843724_724":{"name":"Tax Amount","value":"tax_amount"},"1540894850285_285":{"name":"Discount Amount","value":"discount_amount"},"1540894996810_810":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'tab',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'          => 'shipment_json',
            'title'         => 'Shipment JSON',
            'profile_type'  => 'shipment',
            'file_type'     => 'json',
            'template_html' => '{
    "shipments": [
      {% for shipment in collection %}
      	{
            "increment_id": "{{ shipment.increment_id | escape }}",
            "order": "{{ shipment.order_id | escape }}",
            "created_at": "{{ shipment.created_at | escape }}",
            "order_date": "{{ shipment.order_date | escape }}",
            "order_status": "{{ shipment.order_status | escape }}",
            "store_name": "{{ shipment.store_name | escape }}",
            "customer_name": "{{ shipment.customer_firstname | escape }} {{ shipment.customer_lastname | escape }}",
            "customer_email": "{{ shipment.customer_email | escape }}",
            "customer_group": "{{ shipment.customer_group | escape }}",
            "bill_to_name": "{{ shipment.billingAddress.firstname | escape }} {{ shipment.billingAddress.lastname | escape }}",
            "billing_address": "{{ shipment.billingAddress.street | escape }} {{ shipment.billingAddress.city | escape }} {{ shipment.billingAddress.postcode | escape }}",
            "billing_telephone": "{{ shipment.billingAddress.telephone }}",
            "ship_to_name": "{{ shipment.shippingAddress.firstname | escape }} {{ shipment.shippingAddress.lastname | escape }}",
            "shipping_address": "{{ shipment.shippingAddress.street | escape }} {{ shipment.shippingAddress.city | escape }} {{ shipment.shippingAddress.postcode | escape }}",
            "shipping_telephone": "{{ shipment.shippingAddress.telephone }}",
            "payment_method": "{{ shipment.payment_method | escape }}",
            "shipping_description": "{{ shipment.shipping_description | escape }}",
            "base_shipping_incl_tax": "{{ shipment.base_shipping_incl_tax | escape }}",
            "total_qty": "{{ shipment.total_qty | escape }}",
          	"order_total": {},
            "items": [
              {% for item in shipment.sub_items %}
                {
                    "name": "{{ item.name | escape }}",
                    "sku": "{{ item.sku | escape }}",
                    "qty": "{{ item.qty | escape }}",
              	},
              {% endfor %}
            ]
		},
      {% endfor %}
    ]
}',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];
        $sampleTemplates[] = [
            'name'          => 'shipment_excel_xml',
            'title'         => 'Shipment Excel XML',
            'profile_type'  => 'shipment',
            'file_type'     => 'excel_xml',
            'template_html' => '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">
    <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"></OfficeDocumentSettings>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"></ExcelWorkbook>
    <Worksheet ss:Name="sales_order_grid.xml">
       	<Table>
            <Row>
                <Cell>
                    <Data ss:Type="String">Shipment</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Ship Date</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Order</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Order Date</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Ship-to Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Total Quantity</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Order Status</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Purchased From</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Email</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Group</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Billing Address</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping Address</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Payment Method</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping Information</Data>
                </Cell>
            </Row>
          {% for shipment in collection %}
            <Row>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.increment_id }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.created_at }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.order_id }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.order_date }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.shippingAddress.firstname }} {{ shipment.shippingAddress.lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ shipment.total_qty }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.order_status }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">{{ shipment.store_name }}</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.customer_firstname }} {{ shipment.customer_lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.customer_email }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ shipment.customer_group }}]]></Data>
                </Cell>
    			<Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.billingAddress.street }} {{ shipment.billingAddress.city }} {{ shipment.billingAddress.postcode }} {{ shipment.billingAddress.telephone }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.shippingAddress.street }} {{ shipment.shippingAddress.city }} {{ shipment.shippingAddress.postcode }} {{ shipment.shippingAddress.telephone }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.payment_method }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ shipment.shipping_description }}]]></Data>
                </Cell>
            </Row>
          {% endfor %}
        </Table>
    </Worksheet>
</Workbook>',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];

        $sampleTemplates[] = [
            'name'          => 'shipment_xml',
            'title'         => 'Shipment XML',
            'profile_type'  => 'shipment',
            'file_type'     => 'xml',
            'template_html' => '<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>Sale Order Shipment</title>
          {% for shipment in collection %}
      		<shipment>
                <increment_id><![CDATA[{{ shipment.increment_id }}]]></increment_id>
                <order><![CDATA[{{ shipment.order_id }}]]></order>
                <created_at><![CDATA[{{ shipment.created_at }}]]></created_at>
                <order_date><![CDATA[{{ shipment.order_date }}]]></order_date>
                <order_status><![CDATA[{{ shipment.order_status }}]]></order_status>
                <store_name><![CDATA[{{ shipment.store_name }}]]></store_name>
                <customer_name><![CDATA[{{ shipment.customer_firstname }} {{ shipment.customer_lastname }}]]></customer_name>
                <customer_email><![CDATA[{{ shipment.customer_email }}]]></customer_email>
                <customer_group><![CDATA[{{ shipment.customer_group }}]]></customer_group>
                <bill_to_name><![CDATA[{{ shipment.billingAddress.firstname }} {{ shipment.billingAddress.lastname }}]]></bill_to_name>
                <billing_address><![CDATA[{{ shipment.billingAddress.street }} {{ shipment.billingAddress.city }} {{ shipment.billingAddress.postcode }}]]></billing_address>
                <billing_telephone><![CDATA[{{ shipment.billingAddress.telephone }}]]></billing_telephone>
                <ship_to_name><![CDATA[{{ shipment.shippingAddress.firstname }} {{ shipment.shippingAddress.lastname }}]]></ship_to_name>
                <shipping_address><![CDATA[{{ shipment.shippingAddress.street }} {{ shipment.shippingAddress.city }} {{ shipment.shippingAddress.postcode }}]]></shipping_address>
                <shipping_telephone><![CDATA[{{ shipment.shippingAddress.telephone }}]]></shipping_telephone>
                <payment_method><![CDATA[{{ shipment.payment_method }}]]></payment_method>
                <shipping_description><![CDATA[{{ shipment.shipping_description }}]]></shipping_description>
                <base_shipping_incl_tax><![CDATA[{{ shipment.base_shipping_incl_tax }}]]></base_shipping_incl_tax>
                <total_qty><![CDATA[{{ shipment.total_qty }}]]></total_qty>
              	<order_total>
              	</order_total>
              	<items>
                  {% for item in shipment.sub_items %}
                    <item>
                      <name><![CDATA[{{ item.name }}]]></name>
                      <sku><![CDATA[{{ item.sku }}]]></sku>
                      <qty><![CDATA[{{ item.qty }}{{ item.qty }}]]></qty>
                    </item>
                  {% endfor %}
                </items>
      		</shipment>
          {% endfor %}
    </channel>
</rss>',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];
        $sampleTemplates[] = [
            'name'           => 'shipment_csv',
            'title'          => 'Shipment Csv',
            'profile_type'   => 'shipment',
            'file_type'      => 'csv',
            'template_html'  => '',
            'fields_list'    => '{"1540897316858_858":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ shipment.increment_id }}"},"1540897338234_234":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ shipment.order_id }}"},"1540897352623_623":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ shipment.created_at }}"},"1540897373897_897":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ shipment.order_date }}"},"1540897379670_670":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ shipment.order_status }}"},"1540897385199_199":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ shipment.store_name }}"},"1540897394703_703":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ shipment.customer_firstname }}"},"1540897402398_398":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ shipment.customer_lasttname }}"},"1540897409927_927":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ shipment.customer_email }}"},"1540897415967_967":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ shipment.customer_group }}"},"1540897424303_303":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.firstname }}"},"1540897434413_413":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.lastname }}"},"1540897456928_928":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ shipment.billingAddress.street }}"},"1540897486859_859":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ shipment.billingAddress.city }}"},"1540897495660_660":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.billingAddress.postcode }}"},"1540897505762_762":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.billingAddress.telephone }}"},"1540897525404_404":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.firstname }}"},"1540897535444_444":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.lastname }}"},"1540897543163_163":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.street }}"},"1540897549057_57":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.city }}"},"1540897557482_482":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.postcode }}"},"1540897564900_900":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.telephone }}"},"1540897577586_586":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ shipment.payment_method }}"},"1540897582433_433":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ shipment.shipping_description }}"},"1540897592709_709":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ shipment.base_shipping_incl_tax }}"},"1540897735431_431":{"col_name":"Total Qty","col_type":"attribute","col_attr_val":"total_qty","col_pattern_val":"","col_val":"{{ shipment.total_qty }}"},"1540897746548_548":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540897750645_645":{"name":"Name","value":"name"},"1540897763145_145":{"name":"Sku","value":"sku"},"1540897767609_609":{"name":"Qty","value":"qty"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'shipment_txt',
            'title'          => 'Shipment Txt',
            'profile_type'   => 'shipment',
            'file_type'      => 'txt',
            'template_html'  => '',
            'fields_list'    => '{"1540897316858_858":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ shipment.increment_id }}"},"1540897338234_234":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ shipment.order_id }}"},"1540897352623_623":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ shipment.created_at }}"},"1540897373897_897":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ shipment.order_date }}"},"1540897379670_670":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ shipment.order_status }}"},"1540897385199_199":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ shipment.store_name }}"},"1540897394703_703":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ shipment.customer_firstname }}"},"1540897402398_398":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ shipment.customer_lasttname }}"},"1540897409927_927":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ shipment.customer_email }}"},"1540897415967_967":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ shipment.customer_group }}"},"1540897424303_303":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.firstname }}"},"1540897434413_413":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.lastname }}"},"1540897456928_928":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ shipment.billingAddress.street }}"},"1540897486859_859":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ shipment.billingAddress.city }}"},"1540897495660_660":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.billingAddress.postcode }}"},"1540897505762_762":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.billingAddress.telephone }}"},"1540897525404_404":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.firstname }}"},"1540897535444_444":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.lastname }}"},"1540897543163_163":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.street }}"},"1540897549057_57":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.city }}"},"1540897557482_482":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.postcode }}"},"1540897564900_900":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.telephone }}"},"1540897577586_586":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ shipment.payment_method }}"},"1540897582433_433":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ shipment.shipping_description }}"},"1540897592709_709":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ shipment.base_shipping_incl_tax }}"},"1540897735431_431":{"col_name":"Total Qty","col_type":"attribute","col_attr_val":"total_qty","col_pattern_val":"","col_val":"{{ shipment.total_qty }}"},"1540897746548_548":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540897750645_645":{"name":"Name","value":"name"},"1540897763145_145":{"name":"Sku","value":"sku"},"1540897767609_609":{"name":"Qty","value":"qty"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'shipment_tsv',
            'title'          => 'Shipment Tsv',
            'profile_type'   => 'shipment',
            'file_type'      => 'tsv',
            'template_html'  => '',
            'fields_list'    => '{"1540897316858_858":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ shipment.increment_id }}"},"1540897338234_234":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ shipment.order_id }}"},"1540897352623_623":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ shipment.created_at }}"},"1540897373897_897":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ shipment.order_date }}"},"1540897379670_670":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ shipment.order_status }}"},"1540897385199_199":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ shipment.store_name }}"},"1540897394703_703":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ shipment.customer_firstname }}"},"1540897402398_398":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ shipment.customer_lasttname }}"},"1540897409927_927":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ shipment.customer_email }}"},"1540897415967_967":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ shipment.customer_group }}"},"1540897424303_303":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.firstname }}"},"1540897434413_413":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.lastname }}"},"1540897456928_928":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ shipment.billingAddress.street }}"},"1540897486859_859":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ shipment.billingAddress.city }}"},"1540897495660_660":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.billingAddress.postcode }}"},"1540897505762_762":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.billingAddress.telephone }}"},"1540897525404_404":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.firstname }}"},"1540897535444_444":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.lastname }}"},"1540897543163_163":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.street }}"},"1540897549057_57":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.city }}"},"1540897557482_482":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.postcode }}"},"1540897564900_900":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.telephone }}"},"1540897577586_586":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ shipment.payment_method }}"},"1540897582433_433":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ shipment.shipping_description }}"},"1540897592709_709":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ shipment.base_shipping_incl_tax }}"},"1540897735431_431":{"col_name":"Total Qty","col_type":"attribute","col_attr_val":"total_qty","col_pattern_val":"","col_val":"{{ shipment.total_qty }}"},"1540897746548_548":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540897750645_645":{"name":"Name","value":"name"},"1540897763145_145":{"name":"Sku","value":"sku"},"1540897767609_609":{"name":"Qty","value":"qty"}}}}',
            'field_separate' => 'tab',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'          => 'creditmemo_json',
            'title'         => 'Creditmemo JSON',
            'profile_type'  => 'creditmemo',
            'file_type'     => 'json',
            'template_html' => '{
    "credit_memos": [
      {% for creditmemo in collection %}
      	{
            "increment_id": "{{ creditmemo.increment_id | escape }}",
            "order": "{{ creditmemo.order_id | escape }}",
            "created_at": "{{ creditmemo.created_at | escape }}",
            "order_date": "{{ creditmemo.order_date | escape }}",
            "order_status": "{{ creditmemo.order_status | escape }}",
            "store_name": "{{ creditmemo.store_name | escape }}",
            "customer_name": "{{ creditmemo.customer_firstname | escape }} {{ creditmemo.customer_lastname | escape }}",
            "customer_email": "{{ creditmemo.customer_email | escape }}",
            "customer_group": "{{ creditmemo.customer_group | escape }}",
            "bill_to_name": "{{ creditmemo.billingAddress.firstname | escape }} {{ creditmemo.billingAddress.lastname | escape }}",
            "billing_address": "{{ creditmemo.billingAddress.street | escape }} {{ creditmemo.billingAddress.city | escape }} {{ creditmemo.billingAddress.postcode | escape }}",
            "billing_telephone": "{{ creditmemo.billingAddress.telephone }}",
            "ship_to_name": "{{ creditmemo.shippingAddress.firstname | escape }} {{ creditmemo.shippingAddress.lastname | escape }}",
            "shipping_address": "{{ creditmemo.shippingAddress.street | escape }} {{ creditmemo.shippingAddress.city | escape }} {{ creditmemo.shippingAddress.postcode | escape }}",
            "shipping_telephone": "{{ creditmemo.shippingAddress.telephone }}",
            "payment_method": "{{ creditmemo.payment_method | escape }}",
            "shipping_description": "{{ creditmemo.shipping_description | escape }}",
            "base_shipping_incl_tax": "{{ creditmemo.base_shipping_incl_tax | escape }}",
            "memo_total": {
                "subtotal": "{{ creditmemo.subtotal | escape }}",
                "base_shipping_incl_tax": "{{ creditmemo.base_shipping_incl_tax | escape }}",
                "adjustment": "{{ creditmemo.adjustment | escape }}",
                "adjustment_negative": "{{ creditmemo.adjustment_negative | escape }}",
          		"tax_amount": "{{ creditmemo.tax_amount | escape }}",
                "grand_total": "{{ order.grand_total | escape }}",
            },
            "items": [
              {% for item in creditmemo.sub_items %}
              	{
                    "name": "{{ item.name | escape }}",
                    "sku": "{{ item.sku | escape }}",
                    "price": "{{ item.price | escape }}",
                    "qty": "{{ item.qty | escape }}",
                    "base_row_total": "{{ item.base_row_total | escape }}",
                    "tax_amount": "{{ item.tax_amount | escape }}",
                    "discount_amount": "{{ item.discount_amount | escape }}",
                    "row_total": "{{ item.row_total | escape }}",
                },
              {% endfor %}
            ]
        },
      {% endfor %}
    ]
}',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];
        $sampleTemplates[] = [
            'name'          => 'creditmemo_excel_xml',
            'title'         => 'Creditmemo Excel XML',
            'profile_type'  => 'creditmemo',
            'file_type'     => 'excel_xml',
            'template_html' => '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">
    <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"></OfficeDocumentSettings>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"></ExcelWorkbook>
    <Worksheet ss:Name="sales_order_creditmemo_grid.xml">
       	<Table>
            <Row>
                <Cell>
                    <Data ss:Type="String">Credit Memo</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Created</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Order</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Order Date</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Bill-to Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Status</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Refunded</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Order Status</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Purchased From</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Billing Address</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping Address</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Name</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Email</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Customer Group</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Payment Method</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping Information</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Subtotal</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Shipping &amp; Handling</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Adjustment Refund</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Adjustment Fee</Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String">Grand Total</Data>
                </Cell>
            </Row>
          {% for creditmemo in collection %}
            <Row>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.increment_id }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.created_at }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.order_id }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.order_date }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.billingAddress.firstname }} {{ creditmemo.billingAddress.lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.state_name }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ creditmemo.grand_total }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.order_status }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.store_name }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.billingAddress.street }} {{ creditmemo.billingAddress.city }} {{ creditmemo.billingAddress.postcode }} {{ creditmemo.billingAddress.telephone }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.shippingAddress.street }} {{ creditmemo.shippingAddress.city }} {{ creditmemo.shippingAddress.postcode }} {{ creditmemo.shippingAddress.telephone }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.customer_firstname }} {{ creditmemo.customer_lastname }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.customer_email }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.customer_group }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.payment_method }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.shipping_description }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.subtotal }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ creditmemo.base_shipping_incl_tax }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.adjustment }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="String"><![CDATA[{{ creditmemo.adjustment_negative }}]]></Data>
                </Cell>
                <Cell>
                    <Data ss:Type="Number"><![CDATA[{{ creditmemo.grand_total }}]]></Data>
                </Cell>
            </Row>
          {% endfor %}
        </Table>
    </Worksheet>
</Workbook>',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];

        $sampleTemplates[] = [
            'name'          => 'creditmemo_xml',
            'title'         => 'Creditmemo XML',
            'profile_type'  => 'creditmemo',
            'file_type'     => 'xml',
            'template_html' => '<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>Sale Order Credit Memos</title>
          {% for creditmemo in collection %}
      		<creditmemo>
                <increment_id><![CDATA[{{ creditmemo.increment_id }}]]></increment_id>
                <order><![CDATA[{{ creditmemo.order_id }}]]></order>
                <created_at><![CDATA[{{ creditmemo.created_at }}]]></created_at>
                <order_date><![CDATA[{{ creditmemo.order_date }}]]></order_date>
                <order_status><![CDATA[{{ creditmemo.order_status }}]]></order_status>
                <store_name><![CDATA[{{ creditmemo.store_name }}]]></store_name>
                <customer_name><![CDATA[{{ creditmemo.customer_firstname }} {{ shipment.customer_lastname }}]]></customer_name>
                <customer_email><![CDATA[{{ creditmemo.customer_email }}]]></customer_email>
                <customer_group><![CDATA[{{ creditmemo.customer_group }}]]></customer_group>
                <bill_to_name><![CDATA[{{ creditmemo.billingAddress.firstname }} {{ creditmemo.billingAddress.lastname }}]]></bill_to_name>
                <billing_address><![CDATA[{{ creditmemo.billingAddress.street }} {{ creditmemo.billingAddress.city }} {{ creditmemo.billingAddress.postcode }}]]></billing_address>
                <billing_telephone><![CDATA[{{ creditmemo.billingAddress.telephone }}]]></billing_telephone>
                <ship_to_name><![CDATA[{{ creditmemo.shippingAddress.firstname }} {{ creditmemo.shippingAddress.lastname }}]]></ship_to_name>
                <shipping_address><![CDATA[{{ creditmemo.shippingAddress.street }} {{ creditmemo.shippingAddress.city }} {{ creditmemo.shippingAddress.postcode }}]]></shipping_address>
                <shipping_telephone><![CDATA[{{ creditmemo.shippingAddress.telephone }}]]></shipping_telephone>
                <payment_method><![CDATA[{{ creditmemo.payment_method }}]]></payment_method>
                <shipping_description><![CDATA[{{ creditmemo.shipping_description }}]]></shipping_description>
                <base_shipping_incl_tax><![CDATA[{{ creditmemo.base_shipping_incl_tax }}]]></base_shipping_incl_tax>
              	<memo_total>
                  	<subtotal><![CDATA[{{ creditmemo.subtotal }}]]></subtotal>
                	<base_shipping_incl_tax><![CDATA[{{ creditmemo.base_shipping_incl_tax }}]]></base_shipping_incl_tax>
                    <adjustment><![CDATA[{{ creditmemo.adjustment }}]]></adjustment>
                    <adjustment_negative><![CDATA[{{ creditmemo.adjustment_negative }}]]></adjustment_negative>
                    <tax_amount><![CDATA[{{ creditmemo.tax_amount }}]]></tax_amount>
                    <grand_total><![CDATA[{{ creditmemo.grand_total }}]]></grand_total>
              	</memo_total>
              	<items>
                  {% for item in creditmemo.sub_items %}
                    <item>
                      <name><![CDATA[{{ item.name }}]]></name>
                      <sku><![CDATA[{{ item.sku }}]]></sku>
                      <price><![CDATA[{{ item.price }}]]></price>
                      <qty><![CDATA[{{ item.qty }}{{ item.qty }}]]></qty>
                      <base_row_total><![CDATA[{{ item.base_row_total }}]]></base_row_total>
                      <tax_amount><![CDATA[{{ item.tax_amount }}]]></tax_amount>
                      <discount_amount><![CDATA[{{ item.discount_amount }}]]></discount_amount>
                      <row_total><![CDATA[{{ item.row_total }}]]></row_total>
                    </item>
                  {% endfor %}
                </items>
      		</creditmemo>
          {% endfor %}
    </channel>
</rss>',
            'fields_list'    => null,
            'field_separate' => null,
            'field_around'   => null,
            'include_header' => null
        ];

        $sampleTemplates[] = [
            'name'           => 'creditmemo_csv',
            'title'          => 'Creditmemo Csv',
            'profile_type'   => 'creditmemo',
            'file_type'      => 'csv',
            'template_html'  => '',
            'fields_list'    => '{"1540900677032_32":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ creditmemo.increment_id }}"},"1540900685438_438":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ creditmemo.order_id }}"},"1540900693672_672":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ creditmemo.created_at }}"},"1540900700421_421":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ creditmemo.order_date }}"},"1540900706208_208":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ creditmemo.order_status }}"},"1540900802104_104":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ creditmemo.store_name }}"},"1540900820079_79":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ creditmemo.customer_firstname }}"},"1540900828373_373":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ creditmemo.customer_lasttname }}"},"1540900834151_151":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ creditmemo.customer_email }}"},"1540900842414_414":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ creditmemo.customer_group }}"},"1540900860261_261":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.firstname }}"},"1540900874993_993":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.lastname }}"},"1540900939415_415":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.street }}"},"1540900946868_868":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.city }}"},"1540900952484_484":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.postcode }}"},"1540900960743_743":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.telephone }}"},"1540900966749_749":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.firstname }}"},"1540901004563_563":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.lastname }}"},"1540901016083_83":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.street }}"},"1540901024345_345":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.city }}"},"1540901034165_165":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.postcode }}"},"1540901043259_259":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.telephone }}"},"1540901052582_582":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ creditmemo.payment_method }}"},"1540901056600_600":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ creditmemo.shipping_description }}"},"1540901065753_753":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ creditmemo.base_shipping_incl_tax }}"},"1540901092748_748":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ creditmemo.subtotal }}"},"1540901103946_946":{"col_name":"Adjustment","col_type":"attribute","col_attr_val":"adjustment","col_pattern_val":"","col_val":"{{ creditmemo.adjustment }}"},"1540901362585_585":{"col_name":"Adjustment Negative","col_type":"attribute","col_attr_val":"adjustment_negative","col_pattern_val":"","col_val":"{{ creditmemo.adjustment_negative }}"},"1540901375766_766":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ creditmemo.tax_amount }}"},"1540901381360_360":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ creditmemo.grand_total }}"},"1540901389638_638":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540901392569_569":{"name":"Name","value":"name"},"1540901398883_883":{"name":"Sku","value":"sku"},"1540901403955_955":{"name":"Price","value":"price"},"1540901412895_895":{"name":"Qty","value":"qty"},"1540901423042_42":{"name":"Base Row_total","value":"base_row_total"},"1540901430401_401":{"name":"Tax Amount","value":"tax_amount"},"1540901439443_443":{"name":"Discount Amount","value":"discount_amount"},"1540901445514_514":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'creditmemo_txt',
            'title'          => 'Creditmemo Txt',
            'profile_type'   => 'creditmemo',
            'file_type'      => 'txt',
            'template_html'  => '',
            'fields_list'    => '{"1540900677032_32":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ creditmemo.increment_id }}"},"1540900685438_438":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ creditmemo.order_id }}"},"1540900693672_672":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ creditmemo.created_at }}"},"1540900700421_421":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ creditmemo.order_date }}"},"1540900706208_208":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ creditmemo.order_status }}"},"1540900802104_104":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ creditmemo.store_name }}"},"1540900820079_79":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ creditmemo.customer_firstname }}"},"1540900828373_373":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ creditmemo.customer_lasttname }}"},"1540900834151_151":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ creditmemo.customer_email }}"},"1540900842414_414":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ creditmemo.customer_group }}"},"1540900860261_261":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.firstname }}"},"1540900874993_993":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.lastname }}"},"1540900939415_415":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.street }}"},"1540900946868_868":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.city }}"},"1540900952484_484":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.postcode }}"},"1540900960743_743":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.telephone }}"},"1540900966749_749":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.firstname }}"},"1540901004563_563":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.lastname }}"},"1540901016083_83":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.street }}"},"1540901024345_345":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.city }}"},"1540901034165_165":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.postcode }}"},"1540901043259_259":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.telephone }}"},"1540901052582_582":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ creditmemo.payment_method }}"},"1540901056600_600":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ creditmemo.shipping_description }}"},"1540901065753_753":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ creditmemo.base_shipping_incl_tax }}"},"1540901092748_748":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ creditmemo.subtotal }}"},"1540901103946_946":{"col_name":"Adjustment","col_type":"attribute","col_attr_val":"adjustment","col_pattern_val":"","col_val":"{{ creditmemo.adjustment }}"},"1540901362585_585":{"col_name":"Adjustment Negative","col_type":"attribute","col_attr_val":"adjustment_negative","col_pattern_val":"","col_val":"{{ creditmemo.adjustment_negative }}"},"1540901375766_766":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ creditmemo.tax_amount }}"},"1540901381360_360":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ creditmemo.grand_total }}"},"1540901389638_638":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540901392569_569":{"name":"Name","value":"name"},"1540901398883_883":{"name":"Sku","value":"sku"},"1540901403955_955":{"name":"Price","value":"price"},"1540901412895_895":{"name":"Qty","value":"qty"},"1540901423042_42":{"name":"Base Row_total","value":"base_row_total"},"1540901430401_401":{"name":"Tax Amount","value":"tax_amount"},"1540901439443_443":{"name":"Discount Amount","value":"discount_amount"},"1540901445514_514":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'creditmemo_tsv',
            'title'          => 'Creditmemo Tsv',
            'profile_type'   => 'creditmemo',
            'file_type'      => 'tsv',
            'template_html'  => '',
            'fields_list'    => '{"1540900677032_32":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ creditmemo.increment_id }}"},"1540900685438_438":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ creditmemo.order_id }}"},"1540900693672_672":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ creditmemo.created_at }}"},"1540900700421_421":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ creditmemo.order_date }}"},"1540900706208_208":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ creditmemo.order_status }}"},"1540900802104_104":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ creditmemo.store_name }}"},"1540900820079_79":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ creditmemo.customer_firstname }}"},"1540900828373_373":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ creditmemo.customer_lasttname }}"},"1540900834151_151":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ creditmemo.customer_email }}"},"1540900842414_414":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ creditmemo.customer_group }}"},"1540900860261_261":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.firstname }}"},"1540900874993_993":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.lastname }}"},"1540900939415_415":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.street }}"},"1540900946868_868":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.city }}"},"1540900952484_484":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.postcode }}"},"1540900960743_743":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.telephone }}"},"1540900966749_749":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.firstname }}"},"1540901004563_563":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.lastname }}"},"1540901016083_83":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.street }}"},"1540901024345_345":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.city }}"},"1540901034165_165":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.postcode }}"},"1540901043259_259":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.telephone }}"},"1540901052582_582":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ creditmemo.payment_method }}"},"1540901056600_600":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ creditmemo.shipping_description }}"},"1540901065753_753":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ creditmemo.base_shipping_incl_tax }}"},"1540901092748_748":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ creditmemo.subtotal }}"},"1540901103946_946":{"col_name":"Adjustment","col_type":"attribute","col_attr_val":"adjustment","col_pattern_val":"","col_val":"{{ creditmemo.adjustment }}"},"1540901362585_585":{"col_name":"Adjustment Negative","col_type":"attribute","col_attr_val":"adjustment_negative","col_pattern_val":"","col_val":"{{ creditmemo.adjustment_negative }}"},"1540901375766_766":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ creditmemo.tax_amount }}"},"1540901381360_360":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ creditmemo.grand_total }}"},"1540901389638_638":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540901392569_569":{"name":"Name","value":"name"},"1540901398883_883":{"name":"Sku","value":"sku"},"1540901403955_955":{"name":"Price","value":"price"},"1540901412895_895":{"name":"Qty","value":"qty"},"1540901423042_42":{"name":"Base Row_total","value":"base_row_total"},"1540901430401_401":{"name":"Tax Amount","value":"tax_amount"},"1540901439443_443":{"name":"Discount Amount","value":"discount_amount"},"1540901445514_514":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'tab',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];

        $sampleTemplates[] = [
            'name'           => 'order_ods',
            'title'          => 'Order ODS',
            'profile_type'   => 'order',
            'file_type'      => 'ods',
            'template_html'  => '',
            'fields_list'    => '{"1540889066862_862":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ order.increment_id }}"},"1540889098901_901":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ order.store_name }}"},"1540889115108_108":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ order.created_at }}"},"1540889124094_94":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ order.billingAddress.firstname }}"},"1540889186301_301":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ order.billingAddress.lastname }}"},"1540889210077_77":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ order.shippingAddress.firstname }}"},"1540889236859_859":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ order.shippingAddress.lastname }}"},"1540889250611_611":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ order.shipping_description }}"},"1540889295476_476":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ order.customer_email }}"},"1540889310449_449":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ order.customer_group }}"},"1540889718145_145":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ order.base_shipping_incl_tax }}"},"1540889756500_500":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ order.customer_firstname }}"},"1540889772716_716":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ order.customer_lastname }}"},"1540889814242_242":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ order.payment_method }}"},"1540889866084_84":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ order.subtotal }}"},"1540889882210_210":{"col_name":"Base Grand_total","col_type":"attribute","col_attr_val":"base_grand_total","col_pattern_val":"","col_val":"{{ order.base_grand_total }}"},"1540889896378_378":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ order.grand_total }}"},"1540889913109_109":{"col_name":"Total Paid","col_type":"attribute","col_attr_val":"total_paid","col_pattern_val":"","col_val":"{{ order.total_paid }}"},"1540889921269_269":{"col_name":"Subtotal Refunded","col_type":"attribute","col_attr_val":"subtotal_refunded","col_pattern_val":"","col_val":"{{ order.subtotal_refunded }}"},"1540889925884_884":{"col_name":"Total Due","col_type":"attribute","col_attr_val":"total_due","col_pattern_val":"","col_val":"{{ order.total_due }}"},"1540889967400_400":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540889974759_759":{"name":"Name","value":"name"},"1540889994942_942":{"name":"Sku","value":"sku"},"1540890143378_378":{"name":"Status","value":"status"},"1540890159410_410":{"name":"Original Price","value":"original_price"},"1540890193504_504":{"name":"Price","value":"price"},"1540890203385_385":{"name":"Qty Ordered","value":"qty_ordered"},"1540890216393_393":{"name":"Qty Invoiced","value":"qty_invoiced"},"1540890227420_420":{"name":"Qty Refunded","value":"qty_refunded"},"1540890262046_46":{"name":"Base Row Total Incl Tax","value":"base_row_total_incl_tax"},"1540890295074_74":{"name":"Tax Amount","value":"tax_amount"},"1540890301762_762":{"name":"Tax Percent","value":"tax_percent"},"1540890310967_967":{"name":"Discount Amount","value":"discount_amount"},"1540890319834_834":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'order_xlsx',
            'title'          => 'Order XLSX',
            'profile_type'   => 'order',
            'file_type'      => 'xlsx',
            'template_html'  => '',
            'fields_list'    => '{"1540889066862_862":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ order.increment_id }}"},"1540889098901_901":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ order.store_name }}"},"1540889115108_108":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ order.created_at }}"},"1540889124094_94":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ order.billingAddress.firstname }}"},"1540889186301_301":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ order.billingAddress.lastname }}"},"1540889210077_77":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ order.shippingAddress.firstname }}"},"1540889236859_859":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ order.shippingAddress.lastname }}"},"1540889250611_611":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ order.shipping_description }}"},"1540889295476_476":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ order.customer_email }}"},"1540889310449_449":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ order.customer_group }}"},"1540889718145_145":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ order.base_shipping_incl_tax }}"},"1540889756500_500":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ order.customer_firstname }}"},"1540889772716_716":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ order.customer_lastname }}"},"1540889814242_242":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ order.payment_method }}"},"1540889866084_84":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ order.subtotal }}"},"1540889882210_210":{"col_name":"Base Grand_total","col_type":"attribute","col_attr_val":"base_grand_total","col_pattern_val":"","col_val":"{{ order.base_grand_total }}"},"1540889896378_378":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ order.grand_total }}"},"1540889913109_109":{"col_name":"Total Paid","col_type":"attribute","col_attr_val":"total_paid","col_pattern_val":"","col_val":"{{ order.total_paid }}"},"1540889921269_269":{"col_name":"Subtotal Refunded","col_type":"attribute","col_attr_val":"subtotal_refunded","col_pattern_val":"","col_val":"{{ order.subtotal_refunded }}"},"1540889925884_884":{"col_name":"Total Due","col_type":"attribute","col_attr_val":"total_due","col_pattern_val":"","col_val":"{{ order.total_due }}"},"1540889967400_400":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540889974759_759":{"name":"Name","value":"name"},"1540889994942_942":{"name":"Sku","value":"sku"},"1540890143378_378":{"name":"Status","value":"status"},"1540890159410_410":{"name":"Original Price","value":"original_price"},"1540890193504_504":{"name":"Price","value":"price"},"1540890203385_385":{"name":"Qty Ordered","value":"qty_ordered"},"1540890216393_393":{"name":"Qty Invoiced","value":"qty_invoiced"},"1540890227420_420":{"name":"Qty Refunded","value":"qty_refunded"},"1540890262046_46":{"name":"Base Row Total Incl Tax","value":"base_row_total_incl_tax"},"1540890295074_74":{"name":"Tax Amount","value":"tax_amount"},"1540890301762_762":{"name":"Tax Percent","value":"tax_percent"},"1540890310967_967":{"name":"Discount Amount","value":"discount_amount"},"1540890319834_834":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'invoice_ods',
            'title'          => 'Invoice ODS',
            'profile_type'   => 'invoice',
            'file_type'      => 'ods',
            'template_html'  => '',
            'fields_list'    => '{"1540893649080_80":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ invoice.increment_id }}"},"1540893657566_566":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ invoice.order_id }}"},"1540893671326_326":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ invoice.order_date }}"},"1540893754806_806":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540893762149_149":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ invoice.store_name }}"},"1540893777239_239":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540894096661_661":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ invoice.customer_firstname }}"},"1540894137596_596":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ invoice.customer_lasttname }}"},"1540894155805_805":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ invoice.customer_email }}"},"1540894234290_290":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ invoice.customer_group }}"},"1540894243101_101":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.firstname }}"},"1540894254635_635":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.lastname }}"},"1540894273292_292":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ invoice.billingAddress.street }}"},"1540894292410_410":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ invoice.billingAddress.city }}"},"1540894303282_282":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.billingAddress.postcode }}"},"1540894390760_760":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.billingAddress.telephone }}"},"1540894323146_146":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.firstname }}"},"1540894339586_586":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.lastname }}"},"1540894349301_301":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.street }}"},"1540894361930_930":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.city }}"},"1540894368163_163":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.postcode }}"},"1540894381689_689":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.telephone }}"},"1540894412449_449":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ invoice.payment_method }}"},"1540894471908_908":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ invoice.shipping_description }}"},"1540894698772_772":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ invoice.base_shipping_incl_tax }}"},"1540894727315_315":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ invoice.subtotal }}"},"1540894736608_608":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ invoice.tax_amount }}"},"1540894759127_127":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ invoice.grand_total }}"},"1540894764416_416":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540894769736_736":{"name":"Name","value":"name"},"1540894776343_343":{"name":"Sku","value":"sku"},"1540894788356_356":{"name":"Price","value":"price"},"1540894827811_811":{"name":"Qty","value":"qty"},"1540894838014_14":{"name":"Base Row_total","value":"base_row_total"},"1540894843724_724":{"name":"Tax Amount","value":"tax_amount"},"1540894850285_285":{"name":"Discount Amount","value":"discount_amount"},"1540894996810_810":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'invoice_xlsx',
            'title'          => 'Invoice XLSX',
            'profile_type'   => 'invoice',
            'file_type'      => 'xlsx',
            'template_html'  => '',
            'fields_list'    => '{"1540893649080_80":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ invoice.increment_id }}"},"1540893657566_566":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ invoice.order_id }}"},"1540893671326_326":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ invoice.order_date }}"},"1540893754806_806":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540893762149_149":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ invoice.store_name }}"},"1540893777239_239":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ invoice.created_at }}"},"1540894096661_661":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ invoice.customer_firstname }}"},"1540894137596_596":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ invoice.customer_lasttname }}"},"1540894155805_805":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ invoice.customer_email }}"},"1540894234290_290":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ invoice.customer_group }}"},"1540894243101_101":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.firstname }}"},"1540894254635_635":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.billingAddress.lastname }}"},"1540894273292_292":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ invoice.billingAddress.street }}"},"1540894292410_410":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ invoice.billingAddress.city }}"},"1540894303282_282":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.billingAddress.postcode }}"},"1540894390760_760":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.billingAddress.telephone }}"},"1540894323146_146":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.firstname }}"},"1540894339586_586":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.lastname }}"},"1540894349301_301":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.street }}"},"1540894361930_930":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.city }}"},"1540894368163_163":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.postcode }}"},"1540894381689_689":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ invoice.shippingAddress.telephone }}"},"1540894412449_449":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ invoice.payment_method }}"},"1540894471908_908":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ invoice.shipping_description }}"},"1540894698772_772":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ invoice.base_shipping_incl_tax }}"},"1540894727315_315":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ invoice.subtotal }}"},"1540894736608_608":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ invoice.tax_amount }}"},"1540894759127_127":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ invoice.grand_total }}"},"1540894764416_416":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540894769736_736":{"name":"Name","value":"name"},"1540894776343_343":{"name":"Sku","value":"sku"},"1540894788356_356":{"name":"Price","value":"price"},"1540894827811_811":{"name":"Qty","value":"qty"},"1540894838014_14":{"name":"Base Row_total","value":"base_row_total"},"1540894843724_724":{"name":"Tax Amount","value":"tax_amount"},"1540894850285_285":{"name":"Discount Amount","value":"discount_amount"},"1540894996810_810":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'shipment_ods',
            'title'          => 'Shipment ODS',
            'profile_type'   => 'shipment',
            'file_type'      => 'ods',
            'template_html'  => '',
            'fields_list'    => '{"1540897316858_858":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ shipment.increment_id }}"},"1540897338234_234":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ shipment.order_id }}"},"1540897352623_623":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ shipment.created_at }}"},"1540897373897_897":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ shipment.order_date }}"},"1540897379670_670":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ shipment.order_status }}"},"1540897385199_199":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ shipment.store_name }}"},"1540897394703_703":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ shipment.customer_firstname }}"},"1540897402398_398":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ shipment.customer_lasttname }}"},"1540897409927_927":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ shipment.customer_email }}"},"1540897415967_967":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ shipment.customer_group }}"},"1540897424303_303":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.firstname }}"},"1540897434413_413":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.lastname }}"},"1540897456928_928":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ shipment.billingAddress.street }}"},"1540897486859_859":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ shipment.billingAddress.city }}"},"1540897495660_660":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.billingAddress.postcode }}"},"1540897505762_762":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.billingAddress.telephone }}"},"1540897525404_404":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.firstname }}"},"1540897535444_444":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.lastname }}"},"1540897543163_163":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.street }}"},"1540897549057_57":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.city }}"},"1540897557482_482":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.postcode }}"},"1540897564900_900":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.telephone }}"},"1540897577586_586":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ shipment.payment_method }}"},"1540897582433_433":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ shipment.shipping_description }}"},"1540897592709_709":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ shipment.base_shipping_incl_tax }}"},"1540897735431_431":{"col_name":"Total Qty","col_type":"attribute","col_attr_val":"total_qty","col_pattern_val":"","col_val":"{{ shipment.total_qty }}"},"1540897746548_548":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540897750645_645":{"name":"Name","value":"name"},"1540897763145_145":{"name":"Sku","value":"sku"},"1540897767609_609":{"name":"Qty","value":"qty"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'shipment_xlsx',
            'title'          => 'Shipment XLSX',
            'profile_type'   => 'shipment',
            'file_type'      => 'xlsx',
            'template_html'  => '',
            'fields_list'    => '{"1540897316858_858":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ shipment.increment_id }}"},"1540897338234_234":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ shipment.order_id }}"},"1540897352623_623":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ shipment.created_at }}"},"1540897373897_897":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ shipment.order_date }}"},"1540897379670_670":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ shipment.order_status }}"},"1540897385199_199":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ shipment.store_name }}"},"1540897394703_703":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ shipment.customer_firstname }}"},"1540897402398_398":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ shipment.customer_lasttname }}"},"1540897409927_927":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ shipment.customer_email }}"},"1540897415967_967":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ shipment.customer_group }}"},"1540897424303_303":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.firstname }}"},"1540897434413_413":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.billingAddress.lastname }}"},"1540897456928_928":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ shipment.billingAddress.street }}"},"1540897486859_859":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ shipment.billingAddress.city }}"},"1540897495660_660":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.billingAddress.postcode }}"},"1540897505762_762":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.billingAddress.telephone }}"},"1540897525404_404":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.firstname }}"},"1540897535444_444":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.lastname }}"},"1540897543163_163":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.street }}"},"1540897549057_57":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.city }}"},"1540897557482_482":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.postcode }}"},"1540897564900_900":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ shipment.shippingAddress.telephone }}"},"1540897577586_586":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ shipment.payment_method }}"},"1540897582433_433":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ shipment.shipping_description }}"},"1540897592709_709":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ shipment.base_shipping_incl_tax }}"},"1540897735431_431":{"col_name":"Total Qty","col_type":"attribute","col_attr_val":"total_qty","col_pattern_val":"","col_val":"{{ shipment.total_qty }}"},"1540897746548_548":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540897750645_645":{"name":"Name","value":"name"},"1540897763145_145":{"name":"Sku","value":"sku"},"1540897767609_609":{"name":"Qty","value":"qty"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'creditmemo_ods',
            'title'          => 'Creditmemo ODS',
            'profile_type'   => 'creditmemo',
            'file_type'      => 'ods',
            'template_html'  => '',
            'fields_list'    => '{"1540900677032_32":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ creditmemo.increment_id }}"},"1540900685438_438":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ creditmemo.order_id }}"},"1540900693672_672":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ creditmemo.created_at }}"},"1540900700421_421":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ creditmemo.order_date }}"},"1540900706208_208":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ creditmemo.order_status }}"},"1540900802104_104":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ creditmemo.store_name }}"},"1540900820079_79":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ creditmemo.customer_firstname }}"},"1540900828373_373":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ creditmemo.customer_lasttname }}"},"1540900834151_151":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ creditmemo.customer_email }}"},"1540900842414_414":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ creditmemo.customer_group }}"},"1540900860261_261":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.firstname }}"},"1540900874993_993":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.lastname }}"},"1540900939415_415":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.street }}"},"1540900946868_868":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.city }}"},"1540900952484_484":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.postcode }}"},"1540900960743_743":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.telephone }}"},"1540900966749_749":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.firstname }}"},"1540901004563_563":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.lastname }}"},"1540901016083_83":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.street }}"},"1540901024345_345":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.city }}"},"1540901034165_165":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.postcode }}"},"1540901043259_259":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.telephone }}"},"1540901052582_582":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ creditmemo.payment_method }}"},"1540901056600_600":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ creditmemo.shipping_description }}"},"1540901065753_753":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ creditmemo.base_shipping_incl_tax }}"},"1540901092748_748":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ creditmemo.subtotal }}"},"1540901103946_946":{"col_name":"Adjustment","col_type":"attribute","col_attr_val":"adjustment","col_pattern_val":"","col_val":"{{ creditmemo.adjustment }}"},"1540901362585_585":{"col_name":"Adjustment Negative","col_type":"attribute","col_attr_val":"adjustment_negative","col_pattern_val":"","col_val":"{{ creditmemo.adjustment_negative }}"},"1540901375766_766":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ creditmemo.tax_amount }}"},"1540901381360_360":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ creditmemo.grand_total }}"},"1540901389638_638":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540901392569_569":{"name":"Name","value":"name"},"1540901398883_883":{"name":"Sku","value":"sku"},"1540901403955_955":{"name":"Price","value":"price"},"1540901412895_895":{"name":"Qty","value":"qty"},"1540901423042_42":{"name":"Base Row_total","value":"base_row_total"},"1540901430401_401":{"name":"Tax Amount","value":"tax_amount"},"1540901439443_443":{"name":"Discount Amount","value":"discount_amount"},"1540901445514_514":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $sampleTemplates[] = [
            'name'           => 'creditmemo_xlsx',
            'title'          => 'Creditmemo XLSX',
            'profile_type'   => 'creditmemo',
            'file_type'      => 'xlsx',
            'template_html'  => '',
            'fields_list'    => '{"1540900677032_32":{"col_name":"Increment Id","col_type":"attribute","col_attr_val":"increment_id","col_pattern_val":"","col_val":"{{ creditmemo.increment_id }}"},"1540900685438_438":{"col_name":"Order Id","col_type":"attribute","col_attr_val":"order_id","col_pattern_val":"","col_val":"{{ creditmemo.order_id }}"},"1540900693672_672":{"col_name":"Created At","col_type":"attribute","col_attr_val":"created_at","col_pattern_val":"","col_val":"{{ creditmemo.created_at }}"},"1540900700421_421":{"col_name":"Order Date","col_type":"attribute","col_attr_val":"order_date","col_pattern_val":"","col_val":"{{ creditmemo.order_date }}"},"1540900706208_208":{"col_name":"Order Status","col_type":"attribute","col_attr_val":"order_status","col_pattern_val":"","col_val":"{{ creditmemo.order_status }}"},"1540900802104_104":{"col_name":"Store Name","col_type":"attribute","col_attr_val":"store_name","col_pattern_val":"","col_val":"{{ creditmemo.store_name }}"},"1540900820079_79":{"col_name":"Customer Firstname","col_type":"attribute","col_attr_val":"customer_firstname","col_pattern_val":"","col_val":"{{ creditmemo.customer_firstname }}"},"1540900828373_373":{"col_name":"Customer Lastname","col_type":"attribute","col_attr_val":"customer_lastname","col_pattern_val":"","col_val":"{{ creditmemo.customer_lasttname }}"},"1540900834151_151":{"col_name":"Customer Email","col_type":"attribute","col_attr_val":"customer_email","col_pattern_val":"","col_val":"{{ creditmemo.customer_email }}"},"1540900842414_414":{"col_name":"Customer Group","col_type":"attribute","col_attr_val":"customer_group","col_pattern_val":"","col_val":"{{ creditmemo.customer_group }}"},"1540900860261_261":{"col_name":"BillingAddress.Firstname","col_type":"attribute","col_attr_val":"billingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.firstname }}"},"1540900874993_993":{"col_name":"BillingAddress.Lastname","col_type":"attribute","col_attr_val":"billingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.lastname }}"},"1540900939415_415":{"col_name":"BillingAddress.Street","col_type":"attribute","col_attr_val":"billingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.street }}"},"1540900946868_868":{"col_name":"BillingAddress.City","col_type":"attribute","col_attr_val":"billingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.city }}"},"1540900952484_484":{"col_name":"BillingAddress.Postcode","col_type":"attribute","col_attr_val":"billingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.postcode }}"},"1540900960743_743":{"col_name":"BillingAddress.Telephone","col_type":"attribute","col_attr_val":"billingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.billingAddress.telephone }}"},"1540900966749_749":{"col_name":"ShippingAddress.Firstname","col_type":"attribute","col_attr_val":"shippingAddress.firstname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.firstname }}"},"1540901004563_563":{"col_name":"ShippingAddress.Lastname","col_type":"attribute","col_attr_val":"shippingAddress.lastname","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.lastname }}"},"1540901016083_83":{"col_name":"ShippingAddress.Street","col_type":"attribute","col_attr_val":"shippingAddress.street","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.street }}"},"1540901024345_345":{"col_name":"ShippingAddress.City","col_type":"attribute","col_attr_val":"shippingAddress.city","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.city }}"},"1540901034165_165":{"col_name":"ShippingAddress.Postcode","col_type":"attribute","col_attr_val":"shippingAddress.postcode","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.postcode }}"},"1540901043259_259":{"col_name":"ShippingAddress.Telephone","col_type":"attribute","col_attr_val":"shippingAddress.telephone","col_pattern_val":"","col_val":"{{ creditmemo.shippingAddress.telephone }}"},"1540901052582_582":{"col_name":"Payment Method","col_type":"attribute","col_attr_val":"payment_method","col_pattern_val":"","col_val":"{{ creditmemo.payment_method }}"},"1540901056600_600":{"col_name":"Shipping Description","col_type":"attribute","col_attr_val":"shipping_description","col_pattern_val":"","col_val":"{{ creditmemo.shipping_description }}"},"1540901065753_753":{"col_name":"Base Shipping Incl Tax","col_type":"attribute","col_attr_val":"base_shipping_incl_tax","col_pattern_val":"","col_val":"{{ creditmemo.base_shipping_incl_tax }}"},"1540901092748_748":{"col_name":"Subtotal","col_type":"attribute","col_attr_val":"subtotal","col_pattern_val":"","col_val":"{{ creditmemo.subtotal }}"},"1540901103946_946":{"col_name":"Adjustment","col_type":"attribute","col_attr_val":"adjustment","col_pattern_val":"","col_val":"{{ creditmemo.adjustment }}"},"1540901362585_585":{"col_name":"Adjustment Negative","col_type":"attribute","col_attr_val":"adjustment_negative","col_pattern_val":"","col_val":"{{ creditmemo.adjustment_negative }}"},"1540901375766_766":{"col_name":"Tax Amount","col_type":"attribute","col_attr_val":"tax_amount","col_pattern_val":"","col_val":"{{ creditmemo.tax_amount }}"},"1540901381360_360":{"col_name":"Grand Total","col_type":"attribute","col_attr_val":"grand_total","col_pattern_val":"","col_val":"{{ creditmemo.grand_total }}"},"1540901389638_638":{"col_type":"item","col_attr_val":"0","col_pattern_val":"","col_val":"","items":{"1540901392569_569":{"name":"Name","value":"name"},"1540901398883_883":{"name":"Sku","value":"sku"},"1540901403955_955":{"name":"Price","value":"price"},"1540901412895_895":{"name":"Qty","value":"qty"},"1540901423042_42":{"name":"Base Row_total","value":"base_row_total"},"1540901430401_401":{"name":"Tax Amount","value":"tax_amount"},"1540901439443_443":{"name":"Discount Amount","value":"discount_amount"},"1540901445514_514":{"name":"Row Total","value":"row_total"}}}}',
            'field_separate' => 'comma',
            'field_around'   => 'quotes',
            'include_header' => 1,
        ];
        $collection = $this->defaultTemplate->create()->getCollection();
        foreach ($sampleTemplates as $key => $template) {
            $collection->addFieldToFilter('name', $template['name'])->getFirstItem();
            if ($collection->getSize()) {
                unset($sampleTemplates[$key]);
            }
        }

        if (!empty($sampleTemplates)) {
            $this->moduleDataSetup->getConnection()->insertMultiple(
                $this->moduleDataSetup->getTable('mageplaza_orderexport_defaulttemplate'),
                $sampleTemplates
            );
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function revert()
    {
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
