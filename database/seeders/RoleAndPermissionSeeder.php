<?php

namespace Database\Seeders;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Feature;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = array(
            'Users',
            'Roles',
            'Branches/Shops',
            'Products',
            'Suppliers',
            'Purchase Orders',
            'Purchases',
            'Stock Transfers',
            'Services',
            'Customers',
            'Sales Orders',
            'Proforma Invoices',
            'Invoices',
            'Credit Notes',
            'Delivery Notes',
            'Sales Payments',
            'Sales Returns',
            'Operating Expenses',
            'Cash Flows',
            'Reports',
            'Production Module',
            'HR & Payroll Module',
            'Fixed Assets Management',
            'Hotel Booking',
            'Motorbike Contracts',
            'SMS Notification & Templates',
            'Transportation Trip Logs'
        );

        $feat = Feature::where('name', 'Contracts')->first();
        if (!is_null($feat)) {
            $feat->name = 'Motorbike Contracts';
            $feat->save();
        }
        
        foreach ($features as $key => $value) {
            $feature = Feature::where('name', $value)->first();
            if (is_null($feature)) {
                $feature = new Feature();
                $feature->name = $value;
                $feature->save();
            }
        }

        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        $permissions = array(
            ['feature_id' => 1, 'name' => 'create-user', 'display_name' => 'Create Users'],
            ['feature_id' => 1, 'name' => 'view-user', 'display_name' => 'View Users'],
            ['feature_id' => 1, 'name' => 'edit-user', 'display_name' => 'Edit Users'],
            ['feature_id' => 1, 'name' => 'delete-user', 'display_name' => 'Delete Users'],

            ['feature_id' => 2, 'name' => 'create-role', 'display_name' => 'Create Roles'],
            ['feature_id' => 2, 'name' => 'view-role', 'display_name' => 'View Roles'],
            ['feature_id' => 2, 'name' => 'edit-role', 'display_name' => 'Edit Roles'],
            ['feature_id' => 2, 'name' => 'delete-role', 'display_name' => 'Delete Roles'],
            
            ['feature_id' => 3, 'name' => 'create-shop', 'display_name' => 'Create Shops'],
            ['feature_id' => 3, 'name' => 'view-shop', 'display_name' => 'View Shops'],
            ['feature_id' => 3, 'name' => 'edit-shop', 'display_name' => 'Edit Shops'],
            ['feature_id' => 3, 'name' => 'delete-shop', 'display_name' => 'Delete Shops'],
            ['feature_id' => 3, 'name' => 'edit-settings', 'display_name' => 'Edit Shop Settings'],
            ['feature_id' => 3, 'name' => 'switch-shop', 'display_name' => 'Switch Shops'],
            
            ['feature_id' => 4, 'name' => 'view-product-list', 'display_name' => 'View Products List'],
            ['feature_id' => 4, 'name' => 'create-product', 'display_name' => 'Create Products'],
            ['feature_id' => 4, 'name' => 'view-product', 'display_name' => 'View Products'],
            ['feature_id' => 4, 'name' => 'edit-product', 'display_name' => 'Edit Products'],
            ['feature_id' => 4, 'name' => 'delete-product', 'display_name' => 'Delete Products'],
            ['feature_id' => 4, 'name' => 'deactivate-product', 'display_name' => 'Deactivate Product'],
            ['feature_id' => 4, 'name' => 'view-categories', 'display_name' => 'View Product Categories'],
            ['feature_id' => 4, 'name' => 'view-stock-corrections', 'display_name' => 'View Stock Corrections'],
            ['feature_id' => 4, 'name' => 'create-stock-correction', 'display_name' => 'Create Stock Corrections'],
            ['feature_id' => 4, 'name' => 'delete-stock-correction', 'display_name' => 'Delete Stock Corrections'],
            ['feature_id' => 4, 'name' => 'create-damage', 'display_name' => 'Create Damages'],
            ['feature_id' => 4, 'name' => 'view-damage', 'display_name' => 'View Damages'],
            ['feature_id' => 4, 'name' => 'edit-damage', 'display_name' => 'Edit Damages'],
            ['feature_id' => 4, 'name' => 'delete-damage', 'display_name' => 'Delete Damages'],
            ['feature_id' => 4, 'name' => 'change-price', 'display_name' => 'Change Price'],
            ['feature_id' => 4, 'name' => 'view-stock', 'display_name' => 'View Stocks'],
            ['feature_id' => 4, 'name' => 'edit-stock', 'display_name' => 'Edit Stocks'],
            ['feature_id' => 4, 'name' => 'delete-stock', 'display_name' => 'Delete Stock'],
            ['feature_id' => 4, 'name' => 'view-price-list', 'display_name' => 'View Price List'],

            ['feature_id' => 5, 'name' => 'create-supplier', 'display_name' => 'Create Suppliers'],
            ['feature_id' => 5, 'name' => 'view-supplier', 'display_name' => 'View Suppliers'],
            ['feature_id' => 5, 'name' => 'view-supplier-statement', 'display_name' => "View Supplier's Statement"],
            ['feature_id' => 5, 'name' => 'edit-supplier', 'display_name' => 'Edit Suppliers'],
            ['feature_id' => 5, 'name' => 'delete-supplier', 'display_name' => 'Delete Suppliers'],

            ['feature_id' => 6, 'name' => 'create-purchase-order', 'display_name' => 'Create Purchase Orders'],
            ['feature_id' => 6, 'name' => 'view-purchase-order', 'display_name' => 'View Purchase Orders'],
            ['feature_id' => 6, 'name' => 'edit-purchase-order', 'display_name' => 'Edit Purchase Orders'],
            ['feature_id' => 6, 'name' => 'delete-purchase-order', 'display_name' => 'Delete Purchase Orders'],
            ['feature_id' => 6, 'name' => 'view-purchase-cost', 'display_name' => 'View Purchase Cost'],
            ['feature_id' => 6, 'name' => 'approve-po', 'display_name' => 'Approve Purchase Order'],

            ['feature_id' => 7, 'name' => 'create-purchase', 'display_name' => 'Create Purchases'],
            ['feature_id' => 7, 'name' => 'view-purchase', 'display_name' => 'View Purchases'],
            ['feature_id' => 7, 'name' => 'edit-purchase', 'display_name' => 'Edit Purchases'],
            ['feature_id' => 7, 'name' => 'delete-purchase', 'display_name' => 'Delete Purchases'],
            ['feature_id' => 7, 'name' => 'create-bill', 'display_name' => 'Create Bills'],
            ['feature_id' => 7, 'name' => 'view-bill', 'display_name' => 'View Bills'],
            ['feature_id' => 7, 'name' => 'edit-bill', 'display_name' => 'Edit Bills'],
            ['feature_id' => 7, 'name' => 'delete-bill', 'display_name' => 'Delete Bills'],

            ['feature_id' => 8, 'name' => 'create-stock-transfer', 'display_name' => 'Create Stock Transfers'],
            ['feature_id' => 8, 'name' => 'view-stock-transfer', 'display_name' => 'View Stock Transfers'],
            ['feature_id' => 8, 'name' => 'edit-stock-transfer', 'display_name' => 'Edit Stock Transfers'],
            ['feature_id' => 8, 'name' => 'delete-stock-transfer', 'display_name' => 'Delete Stock Transfers'],
            ['feature_id' => 8, 'name' => 'cancel-stock-transfer', 'display_name' => 'Cancel Stock Transfers'],
            ['feature_id' => 8, 'name' => 'confirm-stock-transfer', 'display_name' => 'Confirm Stock Transfers'],
            ['feature_id' => 8, 'name' => 'receive-stock-transfer', 'display_name' => 'Receive Stock Transfers'],
            ['feature_id' => 8, 'name' => 'modify-received-sto', 'display_name' => 'Modify Received STO'],
            ['feature_id' => 8, 'name' => 'view-all-transfer', 'display_name' => 'View All Stock Transfers'],
            ['feature_id' => 8, 'name' => 'receive-returned-stock', 'display_name' => 'Receive Returned Stock'],
            ['feature_id' => 8, 'name' => 'view-sto-value', 'display_name' => 'View STO Value'],

            ['feature_id' => 9, 'name' => 'create-service', 'display_name' => 'Create Services'],
            ['feature_id' => 9, 'name' => 'view-service', 'display_name' => 'View Services'],
            ['feature_id' => 9, 'name' => 'edit-service', 'display_name' => 'Edit Services'],
            ['feature_id' => 9, 'name' => 'delete-service', 'display_name' => 'Delete Services'],
            ['feature_id' => 9, 'name' => 'create-device', 'display_name' => 'Create Devices'],
            ['feature_id' => 9, 'name' => 'view-device', 'display_name' => 'View Devices'],
            ['feature_id' => 9, 'name' => 'edit-device', 'display_name' => 'Edit Devices'],
            ['feature_id' => 9, 'name' => 'delete-device', 'display_name' => 'Delete Devices'],

            ['feature_id' => 10, 'name' => 'create-customer', 'display_name' => 'Create Customers'],
            ['feature_id' => 10, 'name' => 'view-customer', 'display_name' => 'View Customers'],
            ['feature_id' => 10, 'name' => 'edit-customer', 'display_name' => 'Edit Customers'],
            ['feature_id' => 10, 'name' => 'delete-customer', 'display_name' => 'Delete Customers'],
            ['feature_id' => 10, 'name' => 'activate-customer', 'display_name' => 'Enable & Disable Customer'],

            ['feature_id' => 11, 'name' => 'create-sales-order', 'display_name' => 'Create Sales Orders'],
            ['feature_id' => 11, 'name' => 'view-sales-order', 'display_name' => 'View Sales Orders'],
            ['feature_id' => 11, 'name' => 'edit-sales-order', 'display_name' => 'Edit Sales Orders'],
            ['feature_id' => 11, 'name' => 'delete-sales-order', 'display_name' => 'Delete Sales Orders'],
            ['feature_id' => 11, 'name' => 'approve-sales-order', 'display_name' => 'Approve Sales Orders'],
            ['feature_id' => 11, 'name' => 'package-sales-order', 'display_name' => 'Package Sales Orders'],
            ['feature_id' => 11, 'name' => 'view-order-amount', 'display_name' => 'View Sales Order Amount'],
            ['feature_id' => 11, 'name' => 'print-sales-order', 'display_name' => 'Print Sales Order'],

            ['feature_id' => 12, 'name' => 'create-pro-invoice', 'display_name' => 'Create Proforma Invoice'],
            ['feature_id' => 12, 'name' => 'view-pro-invoice', 'display_name' => 'View Proforma Invoices'],
            ['feature_id' => 12, 'name' => 'edit-pro-invoice', 'display_name' => 'Edit Proforma Invoice'],
            ['feature_id' => 12, 'name' => 'delete-pro-invoice', 'display_name' => 'Delete Proforma Invoice'],
            ['feature_id' => 12, 'name' => 'view-all-pro-invoice', 'display_name' => 'View All Proforma Invoices'],
            ['feature_id' => 12, 'name' => 'approve-pro-invoice', 'display_name' => 'Approve Proforma Invoices'],

            ['feature_id' => 13, 'name' => 'create-invoice', 'display_name' => 'Create Invoices'],
            ['feature_id' => 13, 'name' => 'view-invoice', 'display_name' => 'View Invoices'],
            ['feature_id' => 13, 'name' => 'view-all-invoice', 'display_name' => 'View All Invoices'],
            ['feature_id' => 13, 'name' => 'view-invoices-total', 'display_name' => 'View Invoices Total'],
            ['feature_id' => 13, 'name' => 'print-invoice', 'display_name' => 'Print Invoices'],
            ['feature_id' => 13, 'name' => 'edit-invoice', 'display_name' => 'Edit Invoices'],
            ['feature_id' => 13, 'name' => 'cancel-invoice', 'display_name' => 'Cancel Invoices'],
            ['feature_id' => 13, 'name' => 'delete-invoice', 'display_name' => 'Delete Invoices'],
            ['feature_id' => 13, 'name' => 'offer-discount', 'display_name' => 'Offer Discount'],
            ['feature_id' => 13, 'name' => 'approve-discount', 'display_name' => 'Approve Discounts'],

            ['feature_id' => 14, 'name' => 'create-credit-note', 'display_name' => 'Create Credit Note'],
            ['feature_id' => 14, 'name' => 'view-credit-note', 'display_name' => 'View Credit Note'],
            ['feature_id' => 14, 'name' => 'edit-credit-note', 'display_name' => 'Edit Credit Note'],
            ['feature_id' => 14, 'name' => 'delete-credit-note', 'display_name' => 'Delete Credit Note'],

            ['feature_id' => 15, 'name' => 'create-delivery-note', 'display_name' => 'Create Delivery Note'],
            ['feature_id' => 15, 'name' => 'view-delivery-note', 'display_name' => 'View Delivery Note'],
            ['feature_id' => 15, 'name' => 'edit-delivery-note', 'display_name' => 'Edit Delivery Note'],
            ['feature_id' => 15, 'name' => 'delete-delivery-note', 'display_name' => 'Delete Delivery Note'],
            ['feature_id' => 15, 'name' => 'verify-delivery-note', 'display_name' => 'Delivery Delivery Note'],


            ['feature_id' => 16, 'name' => 'create-sale-payment', 'display_name' => 'Create Sale Payments'],
            ['feature_id' => 16, 'name' => 'view-sale-payment', 'display_name' => 'View Sale Payments'],
            ['feature_id' => 16, 'name' => 'edit-sale-payment', 'display_name' => 'Edit Sale Payments'],
            ['feature_id' => 16, 'name' => 'delete-sale-payment', 'display_name' => 'Delete Sale Payments'],

            ['feature_id' => 17, 'name' => 'create-sales-return', 'display_name' => 'Create Sales Returns'],
            ['feature_id' => 17, 'name' => 'view-sales-return', 'display_name' => 'View Sales Returns'],
            ['feature_id' => 17, 'name' => 'edit-sales-return', 'display_name' => 'Edit Sales Returns'],
            ['feature_id' => 17, 'name' => 'delete-sales-return', 'display_name' => 'Delete Sales Returns'],

            ['feature_id' => 17, 'name' => 'create-refund', 'display_name' => 'Create Refund'],
            ['feature_id' => 17, 'name' => 'view-refund', 'display_name' => 'View Refunds'],
            ['feature_id' => 17, 'name' => 'edit-refund', 'display_name' => 'Edit Refund'],
            ['feature_id' => 17, 'name' => 'approve-refund', 'display_name' => 'Approve Refund'],
            ['feature_id' => 17, 'name' => 'confirm-refund', 'display_name' => 'Confirm Refund'],
            ['feature_id' => 17, 'name' => 'delete-refund', 'display_name' => 'Delete refund'],

            ['feature_id' => 18, 'name' => 'create-expense', 'display_name' => 'Create Expenses'],
            ['feature_id' => 18, 'name' => 'view-expense', 'display_name' => 'View Expenses'],
            ['feature_id' => 18, 'name' => 'view-all-expense', 'display_name' => 'View All Expenses'],
            ['feature_id' => 18, 'name' => 'edit-expense', 'display_name' => 'Edit Expenses'],
            ['feature_id' => 18, 'name' => 'delete-expense', 'display_name' => 'Delete Expenses'],
            ['feature_id' => 18, 'name' => 'approve-expense-payment', 'display_name' => 'Approve Expense Payments'],
            ['feature_id' => 18, 'name' => 'confirm-expense-payment', 'display_name' => 'Confirm Expense Payments'],

            ['feature_id' => 19, 'name' => 'create-cash-flow', 'display_name' => 'Create Cash Flows'],
            ['feature_id' => 19, 'name' => 'view-cash-flow', 'display_name' => 'View Cash Flows'],
            ['feature_id' => 19, 'name' => 'edit-cash-flow', 'display_name' => 'Edit Cash Flows'],
            ['feature_id' => 19, 'name' => 'delete-cash-flow', 'display_name' => 'Delete Cash Flows'],
            ['feature_id' => 19, 'name' => 'create-petty-cash', 'display_name' => 'Create Petty Cash'],
            ['feature_id' => 19, 'name' => 'view-petty-cash', 'display_name' => 'View Petty Cash'],
            ['feature_id' => 19, 'name' => 'edit-petty-cash', 'display_name' => 'Edit Petty Cash'],
            ['feature_id' => 19, 'name' => 'cancel-petty-cash', 'display_name' => 'Cancel Petty Cash'],
            ['feature_id' => 19, 'name' => 'delete-petty-cash', 'display_name' => 'Delete Petty Cash'],
            ['feature_id' => 19, 'name' => 'approve-petty-cash', 'display_name' => 'Approve Petty Cash'],
            ['feature_id' => 19, 'name' => 'confirm-petty-cash-issue', 'display_name' => 'Confirm Petty Cash Issued'],
            ['feature_id' => 19, 'name' => 'confirm-petty-cash-receive', 'display_name' => 'Confirm Petty Cash Received'],


            ['feature_id' => 20, 'name' => 'view-reports', 'display_name' => 'View Reports'],
            ['feature_id' => 20, 'name' => 'view-sales-reports', 'display_name' => 'View Sales Reports'],
            ['feature_id' => 20, 'name' => 'view-finacial-reports', 'display_name' => 'View Financial Reports'],
            ['feature_id' => 20, 'name' => 'view-inventory-reports', 'display_name' => 'View Inventory Reports'],
            ['feature_id' => 20, 'name' => 'view-recyclebin', 'display_name' => 'View Recyclebin'],
            ['feature_id' => 20, 'name' => 'view-action-logs', 'display_name' => 'View Action Logs'],

            ['feature_id' => 21, 'name' => 'access-production-module', 'display_name' => 'Access Production Module'],
            ['feature_id' => 21, 'name' => 'view-production-reports', 'display_name' => 'View Production Reports'],
            ['feature_id' => 21, 'name' => 'manage-pricing-calculator', 'display_name' => 'Manage Pricing Calculator'],
            ['feature_id' => 21, 'name' => 'manage-wips', 'display_name' => 'Manage Work In Progress'],
            ['feature_id' => 21, 'name' => 'manage-production', 'display_name' => 'Manage Production'],
            ['feature_id' => 21, 'name' => 'view-food-production', 'display_name' => 'View Food Production'],
            ['feature_id' => 21, 'name' => 'create-food-production', 'display_name' => 'Create Food Production'],
            ['feature_id' => 21, 'name' => 'edit-food-production', 'display_name' => 'Edit Food Production'],
            ['feature_id' => 21, 'name' => 'delete-food-production', 'display_name' => 'Delete Food Production'],
            // ['feature_id' => 21, 'name' => 'view-raw-material', 'display_name' => 'View Raw Materials'],
            // ['feature_id' => 21, 'name' => 'create-raw-material', 'display_name' => 'Register Raw Material'],
            // ['feature_id' => 21, 'name' => 'edit-raw-material', 'display_name' => 'Edit Raw Material'],
            // ['feature_id' => 21, 'name' => 'delete-raw-material', 'display_name' => 'Delete Raw Material'],
            ['feature_id' => 21, 'name' => 'manage-rm', 'display_name' => 'Manage Raw Materials'],

            // ['feature_id' => 21, 'name' => 'view-packing-material', 'display_name' => 'View Packing Materials'],
            // ['feature_id' => 21, 'name' => 'create-packing-material', 'display_name' => 'Register Packing Material'],
            // ['feature_id' => 21, 'name' => 'edit-packing-material', 'display_name' => 'Edit Packing Material'],
            // ['feature_id' => 21, 'name' => 'delete-packing-material', 'display_name' => 'Delete Packing Material'],
            ['feature_id' => 21, 'name' => 'manage-pm', 'display_name' => 'Manage Packing Materials'],


            ['feature_id' => 21, 'name' => 'manage-labour-costs', 'display_name' => 'Manage Labour Costs'],
            
            ['feature_id' => 22, 'name' => 'access-hr-payroll-module', 'display_name' => 'Access HR & Payroll Module'],

            ['feature_id' => 23, 'name' => 'manage-assets', 'display_name' => 'Manage Fixed Assets'],
            ['feature_id' => 23, 'name' => 'modify-balance-sheet', 'display_name' => 'Modify Balance Sheets'],


            ['feature_id' => 24, 'name' => 'view-booking', 'display_name' => 'View Bookings'],
            ['feature_id' => 24, 'name' => 'create-booking', 'display_name' => 'Create Booking'],
            ['feature_id' => 24, 'name' => 'edit-booking', 'display_name' => 'Edit Booking'],
            ['feature_id' => 24, 'name' => 'cancel-booking', 'display_name' => 'Cancel Booking'],
            ['feature_id' => 24, 'name' => 'delete-booking', 'display_name' => 'Delete Booking'],

            ['feature_id' => 24, 'name' => 'manage-booking-agent', 'display_name' => 'Manage Booking Agents'],

            ['feature_id' => 25, 'name' => 'view-contract', 'display_name' => 'View Contracts'],
            ['feature_id' => 25, 'name' => 'create-contract', 'display_name' => 'Create Contract'],
            ['feature_id' => 25, 'name' => 'edit-contract', 'display_name' => 'Edit Contract'],
            ['feature_id' => 25, 'name' => 'cancel-contract', 'display_name' => 'Cancel Contract'],
            ['feature_id' => 25, 'name' => 'delete-contract', 'display_name' => 'Delete Contract'],
            ['feature_id' => 26, 'name' => 'manage-sms-templates', 'display_name' => 'Manage SMS Templates'],
            ['feature_id' => 26, 'name' => 'send-sms', 'display_name' => 'Send SMS'],
            ['feature_id' => 26, 'name' => 'receive-stock-level-notification', 'display_name' => 'Receive Stock Level Notifications'],
            ['feature_id' => 26, 'name' => 'receive-invoice-notification', 'display_name' => 'Receive Invoice Notifications'],

            ['feature_id' => 27, 'name' => 'create-trip-log', 'display_name' => 'Create Trip Logs'],
            ['feature_id' => 27, 'name' => 'view-trip-log', 'display_name' => 'View Trip Logs'],
            ['feature_id' => 27, 'name' => 'view-all-trips', 'display_name' => 'View All Trip Logs'],
            ['feature_id' => 27, 'name' => 'edit-trip-log', 'display_name' => 'Edit Trip Logs'],
            ['feature_id' => 27, 'name' => 'cancel-trip-invoice', 'display_name' => 'Cancel Trip Invoice'],
            ['feature_id' => 27, 'name' => 'cancel-trip-log', 'display_name' => 'Cancel Trip Logs'],
            ['feature_id' => 27, 'name' => 'delete-trip-log', 'display_name' => 'Delete Trip Logs'],

        );

        foreach ($permissions as $key => $perm) {
            $permission = Permission::where('name', $perm['name'])->first();
            if (is_null($permission)) {
                Permission::create($perm);
            }
        }

        $roles = array(
            [
                'name' => 'super_admin', 'display_name' => 'Administrator', 'description' => 'Smartmauzo system Admin who monitors configuration, and reliable operation of Smart Mauzo App and its infrastracture'
            ]
        );

        foreach ($roles as $key => $role) {
            $roleext = Role::where('name', $role['name'])->first();
            if (is_null($roleext)) {
                Role::create($role);
            }
        }
    }
}
