<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chart;
use App\Models\Settings;
use App\Models\Currency;

class DailyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update daily task';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $xxset=Settings::first();
        if($xxset->auto==1){
            $xxplan=Chart::whereStatus(1)->get();
            if(boomtime($xxset->key_update)>1){
                foreach($xxplan as $val){
                    $symbol=$val['symbol'];
                    $all = @file_get_contents("https://free.currconv.com/api/v7/convert?q=USD_".$symbol."&compact=ultra&apiKey=".$xxset->api);
                    if($all){
                        $price = json_decode($all, true);
                        
                        $plan=Chart::where('symbol', $symbol)->first();
                        $plan->price= round(floatval($price["USD_".$symbol]), 3);
                        $plan->save();
                        if(Currency::where('name', $symbol)->first()->exists()){
                            $currency = Currency::where('name', $symbol)->first();
                            $currency->rate= round(floatval($price["USD_".$symbol]), 3);
                            $currency->save();
                        }
                        $xxset->key_update=date('Y-m-d H:i:s');
                        $xxset->save();
                    
                    }
                }
            }
        }
        $this->info('Task updated');
    }
}
