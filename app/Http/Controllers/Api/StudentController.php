<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\FinancialAccountsResource;
use App\Http\Requests\Api\Delegate\DelegateOrderDetailsRequest;
use App\Http\Resources\Api\Delegate\DelegateOrdersResource;
use App\Http\Requests\Api\Delegate\DelegateLatLng;
use App\Models\ProductServicePrice;
use App\Notifications\OrderNotification;
use App\Services\ProviderService;
use App\Models\ProviderWorkDay;
use App\Services\OrderService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use App\Models\OrderDelegate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Service;
use App\Models\Order;
use Validator;
use Auth;

class StudentController extends Controller {

    use ResponseTrait , PaginationTrait;

    /**  public function delegate main orders . */
    public function delegate_main_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->passes()){

            $ordersIds = OrderDelegate::where(['delegate_id'=>Auth::id()])
                ->whereNull('status')->pluck('order_id')->toArray();

            $orders    = Order::whereIn('id',$ordersIds)->select('lat','lng');

            $distances = getDistanceHaving($orders,$request->lat,$request->lng);

            $data      = DelegateOrdersResource::collection($distances);

            return $this->successData(['orders' => $data]);
        }

        $error = Arr::first(Arr::flatten($validator->messages()->get('*')));

        return response()->json(["key"=>"fail","msg"=>$error]);
    }

    /**  public function user pending orders . */
    public function delegate_pending_orders(DelegateLatLng  $request)
    {
        $orders = Order::where('delegate_id',Auth::id())->whereIn('status',[6,8,9,10,11,12])
            ->with(['provider'])->paginate(10);

        $data = DelegateOrdersResource::collection($orders);

        $pagination = $this->paginationModel($orders);

        return $this->successData(['pagination'=>$pagination,'orders' => $data]);
    }

    /**  public function user finished order . */
    public function delegate_finished_orders(DelegateLatLng $request)
    {
        $orders = Order::where('delegate_id',Auth::id())->whereIn('status',[5,13])
            ->with(['provider'])->paginate(10);

        $data   = DelegateOrdersResource::collection($orders);

        $pagination = $this->paginationModel($orders);

        return $this->successData(['pagination' => $pagination,'orders' => $data]);
    }

    /**  public function delegate order details . */
    public function delegate_order_details(DelegateOrderDetailsRequest $request)
    {
        $order = Order::where(['id'=>$request->order_id])
            ->with(['user','provider'])
            ->first();

        if (isset($request->status)){
            if($request['status'] < $order->status){
                return $this->response('fail',__('apis.invalid_order_status'),[]);
            }
        }

        if (!isset($order)){
            return $this->failMsg(trans('apis.data_incorrect'));
        }
        $orderDelegate = OrderDelegate::where(['delegate_id'=>Auth::id(),'order_id'=>$order->id])->first();
        #Delegate Accepted
        if (isset($request->status) && $request->status == 6){

            $orderDelegate->update(['status'=>'accepted']);

            $order->update(['delegate_id'=>Auth::id()]);

            OrderDelegate::where('order_id',$order->id)->whereNull('status')->delete();

            $chat  = new ChatService();
            $room  = $chat->createRoom(auth()->user(), 0,$order->id);
            $chat->joinRoom($room,$order->user);
            $chat->storeMessage($room,auth()->user(),__('apis.iam_working_order'));

            Notification::send($order->provider, new OrderNotification($order->refresh(),$order->provider,'DELEGATE_ACCEPTED'));
            Notification::send($order->user, new OrderNotification($order->refresh(),$order->user,'DELEGATE_ACCEPTED'));

        }

        #Delegate refused
        if (isset($request->status) && $request->status == 7){
            $orderDelegate->update(['status'=>'refused','reason'=>$request->reason]);
            Notification::send($order->provider, new OrderNotification($order->refresh(),$order->provider,'DELEGATE_CANCELLED'));

        }

        #wallet_balance
        if (!in_array($order->pay_type,[1]) && $request->status == 12){
            Auth::user()->increment('wallet_balance',$order->deliver_price);
        }

        if (isset($request->status)){
            $order->update(['status'=> $request->status]);
        }

        $distance = getDistance($order->provider,$request->lat,$request->lng);
        $data     = $this->OrderService->delegateOrderDetails($order);

        $data['distance'] = numberFormat($distance->distance);

        return $this->successData($data);
    }

    /**  public function order invoice . */
    public function delegate_order_invoice($order)
    {
        $order = Order::where(['id'=>$order])->first();

        if (!isset($order)){
            return $this->failMsg(trans('apis.data_incorrect'));
        }

        $invoice = $this->OrderService->Invoice($order);

        return $this->successData($invoice);
    }

    /**  public function delegate financial accounts . */
    public function delegate_financial_accounts()
    {
        $getOrders = Order::where('delegate_id',Auth::id())->whereNotIn('status',[1,3,7])
            ->paginate(10);

        $sumOrders = Order::where('delegate_id',Auth::id())->whereNotIn('status',[1,3,7])
            ->sum('total_products');

        $orders    = FinancialAccountsResource::collection($getOrders);

        $pagination = $this->paginationModel($getOrders);

        return $this->successData([
            'pagination'   => $pagination,
            'total_orders' => numberFormat($sumOrders),
            'orders'       => $orders
        ]);
    }
}

