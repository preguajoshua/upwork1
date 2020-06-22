@extends('store.layout.app')
<style>
        .collo {
      overflow-y: hidden;
      overflow-x: scroll;
      -webkit-overflow-scrolling: touch;
    }

</style>
@section ('content')
<div class="container-fluid">
    
    
 <div class="row collo">
     
<div>
<div class="card">    
<div class="card-header card-header-primary">
    <h4 class="card-title ">Order List (Assigned Orders)</h4>
</div>
<table class="table">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th>Cart_id</th>
            <th>Cart price</th>
            <th>User</th>
            <th>Delivery_Date</th>
            <th>delivery boy</th>
            <th>Cart Products</th>
            <th class="text-right">Status</th>
        </tr>
    </thead>
    <tbody>
           @if(count($ord)>0)
          @php $i=1; @endphp
          @foreach($ord as $ords)
          <tr>
            <td class="text-center">{{$i}}</td>
            <td>{{$ords->cart_id}}</td>
            <td>{{$ords->total_price}}</td>
            <td>{{$ords->user_name}}<p style="font-size:14px">({{$ords->user_phone}})</p></td>
             <td>{{$ords->delivery_date}}</td>
             <td>{{$ords->boy_name}}<p style="font-size:14px">({{$ords->boy_phone}})</p></td>
            <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal1{{$ords->cart_id}}">Details</button>
            <td class="td-actions text-right">
              
                @if($ords->order_status == 'Confirmed'||$ords->order_status == 'confirmed'||$ords->order_status == 'Confirm'||$ords->order_status == 'confirm')
                <p style="color:orange !important">Confirmed</p>
                @endif
                @if($ords->order_status == 'Out_For_Delivery'||$ords->order_status == 'out_for_delivery'||$ords->order_status == 'delivery_out'||$ords->order_status == 'Delivery_out')
                <p style="color:yellowgreen !important">Out For Delivery</p>
                @endif
                 @if($ords->order_status == 'completed'||$ords->order_status == 'Completed'||$ords->order_status == 'Complete'||$ords->order_status == 'complete')
                <p style="color:green !important">Completed</p>
                @endif
            </td>
        </tr>
          @php $i++; @endphp
                 @endforeach
                  @else
                    <tr>
                      <td>No data found</td>
                    </tr>
                  @endif
    </tbody>
</table>
<div class="pagination justify-content-end" align="right" style="width:100%;float:right !important">{{$ord->links()}}</div>
</div>
</div>
</div>
</div>
<div>
</div>


<!--/////////details model//////////-->
@foreach($ord as $ords)
        <div class="modal fade" id="exampleModal1{{$ords->cart_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        	<div class="modal-dialog" role="document">
        		<div class="modal-content">
        			<div class="modal-header">
        				<h5 class="modal-title" id="exampleModalLabel">Order Details (<b>{{$ords->cart_id}}</b>)</h5>
        					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
        						<span aria-hidden="true">&times;</span>
        					</button>
        			</div>
        			<!--//form-->
        			<table class="table table-bordered" id="example2" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                        <th>product details</th>
                        <th>Order_qty</th>
                        <th>Price</th>
                        </tr>
                      </thead>
                      
                      <tbody>
                      @if(count($details)>0)
                                      @php $i=1; @endphp
                                      
                          <tr>             
                        @foreach($details as $detailss)
                          @if($detailss->cart_id==$ords->cart_id)
                            <td><p><img style="width:25px;height:25px; border-radius:50%" src="{{url($detailss->varient_image)}}" alt="$detailss->product_name">  {{$detailss->product_name}}({{$detailss->quantity}}{{$detailss->unit}})</p>
                            </td>
                            <td>{{$detailss->qty}}</td>
                            <td> 
                            <p><span style="color:grey">{{$detailss->total_price}}</span></p>
                           </td>
    		          	  @endif
                         </tr>
                            @php $i++; @endphp
                            @endforeach
                          @else
                            <tr>
                              <td>No data found</td>
                            </tr>
                                  @endif
                                   
                      </tbody>
                    </table>
        		
        		</div>
        	</div>
        </div>
 @endforeach


    @endsection
</div>