<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RaggiTech\Laravel\Currency\Currency;
use Session;
use \Carbon\Carbon;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\BarcodeSetting;
use App\Models\BusinessType;
use App\Models\ShopCurrency;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    public function index()
    {
        $page = 'Settings';
        $title = 'Settings';
        $title_sw = 'Mipangilio';
        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
             
            $settings = Setting::where('shop_id', $shop->id)->first();
            $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
            if (is_null($settings)) {
                $settings = Setting::create([
                    'shop_id' => $shop->id,
                    'tax_rate' => 18,
                    'inv_no_type' => 'Automatic'
                ]);

                $settings->discount_by_percent = false;
                $settings->save();
            }

            if (is_null($bsetting)) {
                $bsetting = new BarcodeSetting();
                $bsetting->shop_id = $shop->id;
                $bsetting->save();
                return redirect('settings');
            }

            $now = Carbon::now();
            $shidlen = 0;
            $code = '';
            if ($bsetting->code_type === 'EAN8') {
                $shidlen = strlen($shop->id);
                $code = $shop->id.str_pad(3, $bsetting->code_length-$shidlen, '0', STR_PAD_LEFT);
            }elseif ($bsetting->code_type === 'UPCA'){
                $shidlen = strlen($shop->id);
                $code = $shop->id.str_pad(1, $bsetting->code_length-$shidlen, '0', STR_PAD_LEFT);
            }else{
                $shidlen = strlen($shop->id);
                $code = $shop->id.str_pad(1, $bsetting->code_length-$shidlen, '0', STR_PAD_LEFT);
            }
            
            $list = $this->currenciesList();
            // return $list;
            $shopcurrencies = ShopCurrency::where('shop_id', $shop->id)->get();
            $btype = BusinessType::find($shop->business_type_id);
            $btypes = BusinessType::all();
            
            return view('settings.index', compact('page', 'title', 'title_sw', 'shop', 'settings', 'bsetting', 'btypes', 'btype', 'code', 'list', 'shopcurrencies'));
        }else{
            return redirect('user-profile')->with('info', 'Shop not found please try logout and login again');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $bsetting = BarcodeSetting::where('shop_id', $shop->id)->first();
        if (!is_null($bsetting)) {
            if ($request['code_type'] === 'EAN8') {
                $bsetting->code_type = $request['code_type'];
                $bsetting->code_length = 7;
                $bsetting->height = $request['height'];
                $bsetting->width = $request['width'];
                $bsetting->showcode = $request['showcode'];
                $bsetting->save();
            }elseif ($request['code_type'] === 'EAN13') {
                $bsetting->code_type = $request['code_type'];
                $bsetting->code_length = 12;
                $bsetting->height = $request['height'];
                $bsetting->width = $request['width'];
                $bsetting->showcode = $request['showcode'];
                $bsetting->save();
            }elseif ($request['code_type'] === 'UPCA') {
                $bsetting->code_type = $request['code_type'];
                $bsetting->code_length = 10;
                $bsetting->height = $request['height'];
                $bsetting->width = $request['width'];
                $bsetting->showcode = $request['showcode'];
                $bsetting->save();
            }else{
                $bsetting->code_type = $request['code_type'];
                $bsetting->code_length = $request['code_length'];
                $bsetting->height = $request['height'];
                $bsetting->width = $request['width'];
                $bsetting->showcode = $request['showcode'];
                $bsetting->save();
            }            
        }

        $success = 'Barcode setting updated successfully';
        return redirect('settings')->with('success', $success);   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
       $shop = Shop::find(Session::get('shop_id'));
       $shop->business_type_id = $request['business_type_id'];
       $shop->save();

       $message = 'Success!. You have successfully change your Business Type.';
       return redirect()->back()->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Daily Closing Time Settings';
        $title = 'Daily Closing Time Settings';

        $settings = Setting::find(decrypt($id));

        return view('settings.edit', compact('page', 'title', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $taxrate = 18;
        if (!empty($request['tax_rate'])) {
            $taxrate = $request['tax_rate'];
        }

        $setting = Setting::find(decrypt($id));
        if ($request['is_dct_settings']) {
            $setting->dc_time = $request['dc_time'];
            $setting->save();
        }elseif ($request['is_inv_temp_update']) {
            $setting->invoice_temp = $request['invoice_temp'];
            $setting->invoice_title = $request['invoice_title'];
            $setting->invoice_color = $request['invoice_color'];
            $setting->invoice_title_color = $request['invoice_title_color'];
            $setting->invoice_end_note = $request['invoice_end_note'];
            $setting->save();
        }else {
            $setting->tax_rate = $taxrate;
            $setting->allow_more_product_desc = $request['allow_more_product_desc'];
            $setting->inv_no_type = $request['inv_no_type'];
            $setting->is_vat_registered = $request['is_vat_registered'];
            $setting->set_vat_by_default = $request['set_vat_by_default'];
            $setting->estimate_withholding_tax = $request['estimate_withholding_tax'];
            $setting->use_barcode = $request['use_barcode'];
            $setting->always_sell_old = $request['always_sell_old'];
            $setting->allow_sp_less_bp = $request['allow_sp_less_bp'];
            $setting->retail_with_wholesale = $request['retail_with_wholesale'];
            $setting->allow_unit_discount = $request['allow_unit_discount'];
            $setting->discount_by_percent = $request['discount_by_percent'];
            $setting->is_service_per_device = $request['is_service_per_device'];
            $setting->enable_trip_logs = $request['enable_trip_logs'];
            $setting->is_rental_service = $request['is_rental_service'];
            $setting->is_hotel = $request['is_hotel'];
            $setting->show_qty_in_stmt = $request['show_qty_in_stmt'];
            $setting->allow_multi_currency = $request['allow_multi_currency'];
            $setting->enable_exp_date = $request['enable_exp_date'];
            $setting->enable_exp_pay_approval = $request['enable_exp_pay_approval'];
            $setting->show_bd = $request['show_bd'];
            $setting->show_declaration = $request['show_declaration'];
            $setting->show_authorization_sign = $request['show_authorization_sign'];
            $setting->show_discounts = $request['show_discounts'];
            $setting->show_end_note = $request['show_end_note'];
            $setting->always_print_invoice = $request['always_print_invoice'];
            $setting->enable_cpos = $request['enable_cpos'];
            $setting->change_price_for_all_store = $request['change_price_for_all_store'];
            $setting->enable_sale_approval = $request['enable_sale_approval'];
            $setting->sale_with_low_stock = $request['sale_with_low_stock'];
            $setting->allow_initiate_sto_from_invoice = $request['allow_initiate_sto_from_invoice'];
            $setting->sp_mindays = $request['sp_mindays'];
            $setting->is_categorized = $request['is_categorized'];
            $setting->is_filling_station = $request['is_filling_station'];
            $setting->generate_barcode = $request['generate_barcode'];
            $setting->is_manuf_with_merch = $request['is_manuf_with_merch'];
            $setting->is_livestock = $request['is_livestock'];
            $setting->is_manufacturing_with_service = $request['is_manufacturing_with_service'];
            $setting->use_production_module = $request['use_production_module'];
            $setting->enable_packaging = $request['enable_packaging'];
            $setting->enable_hr_payroll_module = $request['enable_hr_payroll_module'];
            $setting->save();
        }
        
        return redirect()->back()->with('success', 'Your Settings were updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function invoiceTemplateSetting()
    {
        $page = 'Invoice Template Settings';
        $title = 'Invoice Template Settings';
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $templates = array(
            'v1' => 'templates/v1.png',
            'v2' => 'templates/v2.png',
            'v3' => 'templates/v3.png',
            // 'v4' => 'templates/v4.png',
        );

        $colors = array(
            '#DB8700', '#0459c6', '#a99f03', '#04899e', '#389e04', '#e56806', '#7306e6', '#e50606', '#e5ac06', '#049e72', '#9e0480', '#56049e', '#dbaf61'
        );
        return view('settings.invoice-templates', compact('page', 'title', 'settings', 'templates', 'colors'));
    }

    public function upgrade()
    {
        $shop = Shop::find(Session::get('shop_id'));
        $shop->subscription_type_id = 2;
        $shop->save();

        return redirect('setting')->with('success', 'You have successful Upgraded your account to Premium Version. Enjoy our Easy, Efficient and Powerfully Software to Manage Your Business.');
    }

    public function downgrade()
    {
        return view('downgrade');
    }

    public function setCurrency(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $settings = Setting::where('shop_id', $shop->id)->first();
        $shopcurrencies = ShopCurrency::where('shop_id', $shop->id)->count();
        if ($shopcurrencies == 0) {
            foreach ($this->currenciesList() as $key => $cur) {
                if ($key == $request['code']) {
                    $shopcurr = new ShopCurrency();
                    $shopcurr->shop_id = $shop->id;
                    $shopcurr->code = $request['code'];
                    $shopcurr->symbol = $cur['symbol'];
                    $shopcurr->name = $cur['name'];
                    $shopcurr->is_default = true;
                    $shopcurr->save();
                }
            }
            
            return redirect('settings')->with('success', 'Currency was added successfully');
        }elseif ($shopcurrencies >= 1 && $settings->allow_multi_currency) {
            foreach ($this->currenciesList() as $key => $cur) {
                if ($key == $request['code']) {
                    $shopcurr = new ShopCurrency();
                    $shopcurr->shop_id = $shop->id;
                    $shopcurr->code = $request['code'];
                    $shopcurr->symbol = $cur['symbol'];
                    $shopcurr->name = $cur['name'];
                    $shopcurr->save();
                }
            }
            return redirect('settings')->with('success', 'Currency was added successfully');
        }else{
            return redirect('settings')->with('warning', 'Please allow multi currency first to add more Currencies');
        }
    }

    public function removeCurrency($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $shopcurr = ShopCurrency::find(decrypt($id));
        if ($shopcurr->is_default) {
            return redirect('settings')->with('info', 'Sorry! You cannot remove default currency before setting another.');
        }else{
            $shopcurr->delete();
            return redirect('settings')->with('success', 'Currency was removed successfully');
        }
    }

    public function makeDefaultCurrency($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $dfc = ShopCurrency::where('shop_id', $shop->id)->where('is_default', true)->first();
        $dfc->is_default = false;
        $dfc->save();

        $shopcurr = ShopCurrency::find(decrypt($id));
        $shopcurr->is_default = true;
        $shopcurr->save();

        return redirect('settings')->with('success', 'Default Currency changed successfully');
    }

    public function currenciesList()
    {
        return [
            'TZS' => [
                'name' => 'Tanzanian Shilling',
                'symbol' => 'TSh',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'USD' => [
                'name' => 'US Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'CAD' => [
                'name' => 'Canadian Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'EUR' => [
                'name' => 'Euro',
                'symbol' => '€',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'AED' => [
                'name' => 'United Arab Emirates Dirham',
                'symbol' => 'د.إ.‏',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'AFN' => [
                'name' => 'Afghan Afghani',
                'symbol' => '؋',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'ALL' => [
                'name' => 'Albanian Lek',
                'symbol' => 'Lek',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'AMD' => [
                'name' => 'Armenian Dram',
                'symbol' => 'դր.',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'AOA' => [
                'name' => 'Angolan Kwanza',
                'symbol' => 'Kz',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'ARS' => [
                'name' => 'Argentine Peso',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'AUD' => [
                'name' => 'Australian Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'AWG' => [
                'name' => 'Aruban Florin',
                'symbol' => 'ƒ',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'AZN' => [
                'name' => 'Azerbaijani Manat',
                'symbol' => 'ман.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'BAM' => [
                'name' => 'Bosnia-Herzegovina Convertible Mark',
                'symbol' => 'KM',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'BDT' => [
                'name' => 'Bangladeshi Taka',
                'symbol' => '৳',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'BGN' => [
                'name' => 'Bulgarian Lev',
                'symbol' => 'лв.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'BHD' => [
                'name' => 'Bahraini Dinar',
                'symbol' => 'د.ب.‏',
                'pre_symbol' => false,
                'decimals' => 3
            ],
            'BIF' => [
                'name' => 'Burundian Franc',
                'symbol' => 'FBu',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'BND' => [
                'name' => 'Brunei Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'BOB' => [
                'name' => 'Bolivian Boliviano',
                'symbol' => 'Bs',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'BRL' => [
                'name' => 'Brazilian Real',
                'symbol' => 'R$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'BSD' => [
                'name' => 'Bahamian Dollar',
                'symbol' => 'B$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'BWP' => [
                'name' => 'Botswanan Pula',
                'symbol' => 'P',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'BYN' => [
                'name' => 'Belarusian Ruble',
                'symbol' => 'руб.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'BZD' => [
                'name' => 'Belize Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'CDF' => [
                'name' => 'Congolese Franc',
                'symbol' => 'FrCD',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'CHF' => [
                'name' => 'Swiss Franc',
                'symbol' => 'CHF',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'CLP' => [
                'name' => 'Chilean Peso',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 0
            ],
            'CNY' => [
                'name' => 'Chinese Yuan',
                'symbol' => 'CN¥',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'COP' => [
                'name' => 'Colombian Peso',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 0
            ],
            'CRC' => [
                'name' => 'Costa Rican Colón',
                'symbol' => '₡',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'CVE' => [
                'name' => 'Cape Verdean Escudo',
                'symbol' => 'CV$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'CZK' => [
                'name' => 'Czech Republic Koruna',
                'symbol' => 'Kč',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'DJF' => [
                'name' => 'Djiboutian Franc',
                'symbol' => 'Fdj',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'DKK' => [
                'name' => 'Danish Krone',
                'symbol' => 'kr',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'DOP' => [
                'name' => 'Dominican Peso',
                'symbol' => 'RD$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'DZD' => [
                'name' => 'Algerian Dinar',
                'symbol' => 'د.ج.‏',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'EEK' => [
                'name' => 'Estonian Kroon',
                'symbol' => 'kr',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'EGP' => [
                'name' => 'Egyptian Pound',
                'symbol' => 'ج.م.‏',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'ERN' => [
                'name' => 'Eritrean Nakfa',
                'symbol' => 'Nfk',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'ETB' => [
                'name' => 'Ethiopian Birr',
                'symbol' => 'Br',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'GBP' => [
                'name' => 'British Pound Sterling',
                'symbol' => '£',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'GEL' => [
                'name' => 'Georgian Lari',
                'symbol' => 'GEL',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'GHS' => [
                'name' => 'Ghanaian Cedi',
                'symbol' => 'GH₵',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'GNF' => [
                'name' => 'Guinean Franc',
                'symbol' => 'FG',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'GTQ' => [
                'name' => 'Guatemalan Quetzal',
                'symbol' => 'Q',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'HKD' => [
                'name' => 'Hong Kong Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'HNL' => [
                'name' => 'Honduran Lempira',
                'symbol' => 'L',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'HRK' => [
                'name' => 'Croatian Kuna',
                'symbol' => 'kn',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'HUF' => [
                'name' => 'Hungarian Forint',
                'symbol' => 'Ft',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'IDR' => [
                'name' => 'Indonesian Rupiah',
                'symbol' => 'Rp',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'ILS' => [
                'name' => 'Israeli New Sheqel',
                'symbol' => '₪',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'INR' => [
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'IQD' => [
                'name' => 'Iraqi Dinar',
                'symbol' => 'د.ع.‏',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'IRR' => [
                'name' => 'Iranian Rial',
                'symbol' => '﷼',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'ISK' => [
                'name' => 'Icelandic Króna',
                'symbol' => 'kr',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'JMD' => [
                'name' => 'Jamaican Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'JOD' => [
                'name' => 'Jordanian Dinar',
                'symbol' => 'د.أ.‏',
                'pre_symbol' => false,
                'decimals' => 3
            ],
            'JPY' => [
                'name' => 'Japanese Yen',
                'symbol' => '￥',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'KES' => [
                'name' => 'Kenyan Shilling',
                'symbol' => 'Ksh',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'KHR' => [
                'name' => 'Cambodian Riel',
                'symbol' => '៛',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'KMF' => [
                'name' => 'Comorian Franc',
                'symbol' => 'FC',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'KRW' => [
                'name' => 'South Korean Won',
                'symbol' => '₩',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'KWD' => [
                'name' => 'Kuwaiti Dinar',
                'symbol' => 'د.ك.‏',
                'pre_symbol' => false,
                'decimals' => 3
            ],
            'KZT' => [
                'name' => 'Kazakhstani Tenge',
                'symbol' => 'тңг.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'LBP' => [
                'name' => 'Lebanese Pound',
                'symbol' => 'ل.ل.‏',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'LKR' => [
                'name' => 'Sri Lankan Rupee',
                'symbol' => 'SL Re',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'LTL' => [
                'name' => 'Lithuanian Litas',
                'symbol' => 'Lt',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'LVL' => [
                'name' => 'Latvian Lats',
                'symbol' => 'Ls',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'LYD' => [
                'name' => 'Libyan Dinar',
                'symbol' => 'د.ل.‏',
                'pre_symbol' => false,
                'decimals' => 3
            ],
            'MAD' => [
                'name' => 'Moroccan Dirham',
                'symbol' => 'د.م.‏',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'MDL' => [
                'name' => 'Moldovan Leu',
                'symbol' => 'MDL',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'MGA' => [
                'name' => 'Malagasy Ariary',
                'symbol' => 'MGA',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'MKD' => [
                'name' => 'Macedonian Denar',
                'symbol' => 'MKD',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'MMK' => [
                'name' => 'Myanma Kyat',
                'symbol' => 'K',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'MOP' => [
                'name' => 'Macanese Pataca',
                'symbol' => 'MOP$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'MUR' => [
                'name' => 'Mauritian Rupee',
                'symbol' => 'MURs',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'MXN' => [
                'name' => 'Mexican Peso',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'MYR' => [
                'name' => 'Malaysian Ringgit',
                'symbol' => 'RM',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'MZN' => [
                'name' => 'Mozambican Metical',
                'symbol' => 'MTn',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'NAD' => [
                'name' => 'Namibian Dollar',
                'symbol' => 'N$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'NGN' => [
                'name' => 'Nigerian Naira',
                'symbol' => '₦',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'NIO' => [
                'name' => 'Nicaraguan Córdoba',
                'symbol' => 'C$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'NOK' => [
                'name' => 'Norwegian Krone',
                'symbol' => 'kr',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'NPR' => [
                'name' => 'Nepalese Rupee',
                'symbol' => 'नेरू',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'NZD' => [
                'name' => 'New Zealand Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'OMR' => [
                'name' => 'Omani Rial',
                'symbol' => 'ر.ع.‏',
                'pre_symbol' => false,
                'decimals' => 3
            ],
            'PAB' => [
                'name' => 'Panamanian Balboa',
                'symbol' => 'B/.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'PEN' => [
                'name' => 'Peruvian Nuevo Sol',
                'symbol' => 'S/.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'PHP' => [
                'name' => 'Philippine Peso',
                'symbol' => '₱',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'PKR' => [
                'name' => 'Pakistani Rupee',
                'symbol' => '₨',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'PLN' => [
                'name' => 'Polish Zloty',
                'symbol' => 'zł',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'PYG' => [
                'name' => 'Paraguayan Guarani',
                'symbol' => '₲',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'QAR' => [
                'name' => 'Qatari Rial',
                'symbol' => 'ر.ق.‏',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'RON' => [
                'name' => 'Romanian Leu',
                'symbol' => 'RON',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'RSD' => [
                'name' => 'Serbian Dinar',
                'symbol' => 'дин.',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'RUB' => [
                'name' => 'Russian Ruble',
                'symbol' => '₽.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'RWF' => [
                'name' => 'Rwandan Franc',
                'symbol' => 'FR',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'SAR' => [
                'name' => 'Saudi Riyal',
                'symbol' => 'ر.س.‏',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'SDG' => [
                'name' => 'Sudanese Pound',
                'symbol' => 'SDG',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'SEK' => [
                'name' => 'Swedish Krona',
                'symbol' => 'kr',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'SGD' => [
                'name' => 'Singapore Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'SOS' => [
                'name' => 'Somali Shilling',
                'symbol' => 'Ssh',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'SYP' => [
                'name' => 'Syrian Pound',
                'symbol' => 'ل.س.‏',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'THB' => [
                'name' => 'Thai Baht',
                'symbol' => '฿',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'TND' => [
                'name' => 'Tunisian Dinar',
                'symbol' => 'د.ت.‏',
                'pre_symbol' => false,
                'decimals' => 3
            ],
            'TOP' => [
                'name' => 'Tongan Paʻanga',
                'symbol' => 'T$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'TRY' => [
                'name' => 'Turkish Lira',
                'symbol' => 'TL',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'TTD' => [
                'name' => 'Trinidad And Tobago Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'TWD' => [
                'name' => 'New Taiwan Dollar',
                'symbol' => 'NT$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'UAH' => [
                'name' => 'Ukrainian Hryvnia',
                'symbol' => '₴',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'UGX' => [
                'name' => 'Ugandan Shilling',
                'symbol' => 'USh',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'UYU' => [
                'name' => 'Uruguayan Peso',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'UZS' => [
                'name' => 'Uzbekistan Som',
                'symbol' => 'UZS',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'VEF' => [
                'name' => 'Venezuelan Bolívar',
                'symbol' => 'Bs.F.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'VND' => [
                'name' => 'Vietnamese Dong',
                'symbol' => '₫',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'XAF' => [
                'name' => 'Cfa Franc Beac',
                'symbol' => 'FCFA',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'XOF' => [
                'name' => 'Cfa Franc Bceao',
                'symbol' => 'CFA',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'YER' => [
                'name' => 'Yemeni Rial',
                'symbol' => 'ر.ي.‏',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'ZAR' => [
                'name' => 'South African Rand',
                'symbol' => 'R',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'ZMK' => [
                'name' => 'Zambian Kwacha',
                'symbol' => 'ZK',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'ZWL' => [
                'name' => 'Zimbabwean Dollar',
                'symbol' => 'ZWL$',
                'pre_symbol' => true,
                'decimals' => 0
            ],
            'BBD' => [
                'name' => 'Barbados Dollar',
                'symbol' => 'Bds$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'BYR' => [
                'name' => 'Belarusian Ruble',
                'symbol' => 'Br',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'BMD' => [
                'name' => 'Bermudian Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'BTN' => [
                'name' => 'Ngultrum',
                'symbol' => 'Nu.',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'KYD' => [
                'name' => 'Cayman Islands Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'CUP' => [
                'name' => 'Cuban Peso',
                'symbol' => '₱',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'FKP' => [
                'name' => 'Falkland Islands Pound',
                'symbol' => '£',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'FJD' => [
                'name' => 'Fiji Dollar',
                'symbol' => 'FJ$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'GMD' => [
                'name' => 'Dalasi',
                'symbol' => 'D',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'GIP' => [
                'name' => 'Gibraltar Pound',
                'symbol' => '£',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'GYD' => [
                'name' => 'Guyanese Dollar',
                'symbol' => 'G$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'HTG' => [
                'name' => 'Haiti Gourde',
                'symbol' => 'G',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'KPW' => [
                'name' => 'North Korean Won',
                'symbol' => '₩',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'KGS' => [
                'name' => 'Kyrgyzstani Som',
                'symbol' => 'Лв',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'LAK' => [
                'name' => 'Lao Kip',
                'symbol' => '₭',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'LSL' => [
                'name' => 'Lesotho Loti',
                'symbol' => 'M',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'LRD' => [
                'name' => 'Liberian Dollar',
                'symbol' => 'L$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'MWK' => [
                'name' => 'Malawian Kwacha',
                'symbol' => 'MK',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'MVR' => [
                'name' => 'Maldivian Rufiyaa',
                'symbol' => 'Rf',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'MRO' => [
                'name' => 'Mauritanian Ouguiya',
                'symbol' => 'UM',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'MUR' => [
                'name' => 'Mauritian Rupee',
                'symbol' => 'Rs',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'MNT' => [
                'name' => 'Mongolian Tögrög',
                'symbol' => '₮',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'PGK' => [
                'name' => 'Papua New Guinean Kina',
                'symbol' => 'K',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'SHP' => [
                'name' => 'St Helena Pound',
                'symbol' => '£',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'XCD' => [
                'name' => 'Eastern Caribbean Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'WST' => [
                'name' => 'Samoan Tālā',
                'symbol' => 'SAT',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'STD' => [
                'name' => 'São Tomé And Príncipe Dobra',
                'symbol' => 'Db',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'SCR' => [
                'name' => 'Seychellois Rupee',
                'symbol' => 'SR',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'SLL' => [
                'name' => 'Sierra Leonean Leone',
                'symbol' => 'Le',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'SBD' => [
                'name' => 'Solomon Islands Dollar',
                'symbol' => 'Si$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'SSP' => [
                'name' => 'South Sudanese Pound',
                'symbol' => '£',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'SRD' => [
                'name' => 'Suriname Dollar',
                'symbol' => '$',
                'pre_symbol' => true,
                'decimals' => 2
            ],
            'SZL' => [
                'name' => 'Swazi Lilangeni',
                'symbol' => 'E',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'TJS' => [
                'name' => 'Tajikistani Somoni',
                'symbol' => 'SM',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'TMT' => [
                'name' => 'Turkmenistan Manat',
                'symbol' => 'T',
                'pre_symbol' => false,
                'decimals' => 2
            ],
            'VUV' => [
                'name' => 'Vanuatu Vatu',
                'symbol' => 'VT',
                'pre_symbol' => false,
                'decimals' => 0
            ],
            'XPF' => [
                'name' => 'CFP Franc',
                'symbol' => '₣',
                'pre_symbol' => false,
                'decimals' => 0
            ],
        ];

    }
}
