<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PmItem;
use App\Models\PmUseItem;
use App\Models\PmDamage;
use App\Models\PmTransferItem;
use App\Models\TpmItem;
use Log;

class PMUpdaterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shop;
    protected $id;
    /**
     * Create a new job instance.
     */
    public function __construct($id, $shop)
    {
        $this->shop = $shop;
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Updating PM Stock');
        $shop = $this->shop;
        $pm_id = $this->id;
        $shop_packing_material = $shop->packingMaterials()->where('packing_material_id', $pm_id)->where('is_deleted' , false)->first();
        if (!is_null($shop_packing_material)) {
            $purchased = PmItem::where('packing_material_id', $pm_id)->where('shop_id', $shop->id)->where('is_deleted' , false)->sum('qty');
            $used = PmUseItem::where('packing_material_id', $pm_id)->where('shop_id', $shop->id)->sum('quantity');
            $damaged = PmDamage::where('packing_material_id', $pm_id)->where('shop_id', $shop->id)->sum('quantity');
            $transfered = PmTransferItem::where('packing_material_id', $pm_id)->join('pm_transfers', 'pm_transfers.id', '=', 'pm_transfer_items.pm_transfer_id')->where('shop_id', $shop->id)->sum('qty');
            Log::info('transfered '.$transfered);
            $stpmitem = TpmItem::where('source_pm_id', $pm_id)->join('transfer_orders', 'transfer_orders.id', '=', 'tpm_items.order_id')->where('shop_id', $shop->id)->sum('source_pm_qty');
            $dtpmitem = TpmItem::where('destin_pm_id', $pm_id)->join('transfer_orders', 'transfer_orders.id', '=', 'tpm_items.order_id')->where('shop_id', $shop->id)->sum('destin_pm_qty');

            $instore = ($purchased+$stpmitem)-($used+$damaged+$transfered+$dtpmitem);
            $shop_packing_material->pivot->in_store = $instore;
            $shop_packing_material->pivot->save();
        }else{
            Log::info('PM not found');
        }
    }
}
