<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-tooth mr-1"></i> Pemeriksaan Odontogram </h3>
        <div class="card-tools">
            {{-- <button type="button" class="btn btn-tool" wire:click="collapsed" data-card-widget="maximize">
                <i class="fas fa-lg fa-expand"></i>     
            </button> --}}
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-odontogram">
            <table style="margin: 0 auto; width: 450px; text-align: center;">
                <tr>
                    <td>8</td><td>7</td><td>6</td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td>
                </tr>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_18 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_18')->first();
                        @endphp
                        @if ($gg_18)
                            <img src="{{ url('images/'.$gg_18->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_17 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_17')->first();
                        @endphp
                        @if ($gg_17)
                            <img src="{{ url('images/'.$gg_17->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_16 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_16')->first();
                        @endphp
                        @if ($gg_16)
                            <img src="{{ url('images/'.$gg_16->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_15 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_15')->first();
                        @endphp
                        @if ($gg_15)
                            <img src="{{ url('images/'.$gg_15->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_14 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_14')->first();
                        @endphp
                        @if ($gg_14)
                            <img src="{{ url('images/'.$gg_14->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_13 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_13')->first();
                        @endphp
                        @if ($gg_13)
                            <img src="{{ url('images/'.$gg_13->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_12 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_12')->first();
                        @endphp
                        @if ($gg_12)
                            <img src="{{ url('images/'.$gg_12->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_11 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_11')->first();
                        @endphp
                        @if ($gg_11)
                            <img src="{{ url('images/'.$gg_11->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_21 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_21')->first();
                        @endphp
                        @if ($gg_21)
                            <img src="{{ url('images/'.$gg_21->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_22 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_22')->first();
                        @endphp
                        @if ($gg_22)
                            <img src="{{ url('images/'.$gg_22->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_23 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_23')->first();
                        @endphp
                        @if ($gg_23)
                            <img src="{{ url('images/'.$gg_23->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_24 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_24')->first();
                        @endphp
                        @if ($gg_24)
                            <img src="{{ url('images/'.$gg_24->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_25 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_25')->first();
                        @endphp
                        @if ($gg_25)
                            <img src="{{ url('images/'.$gg_25->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_26 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_26')->first();
                        @endphp
                        @if ($gg_26)
                            <img src="{{ url('images/'.$gg_26->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_27 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_27')->first();
                        @endphp
                        @if ($gg_27)
                            <img src="{{ url('images/'.$gg_27->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_28 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_28')->first();
                        @endphp
                        @if ($gg_28)
                            <img src="{{ url('images/'.$gg_28->value.'.png') }}" alt="">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="height: 5px;"> </td>
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_55 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_55')->first();
                        @endphp
                        @if ($gg_55)
                            <img src="{{ url('images/'.$gg_55->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_54 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_54')->first();
                        @endphp
                        @if ($gg_54)
                            <img src="{{ url('images/'.$gg_54->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_53 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_53')->first();
                        @endphp
                        @if ($gg_53)
                            <img src="{{ url('images/'.$gg_53->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_52 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_52')->first();
                        @endphp
                        @if ($gg_52)
                            <img src="{{ url('images/'.$gg_52->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_51 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_51')->first();
                        @endphp
                        @if ($gg_51)
                            <img src="{{ url('images/'.$gg_51->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_61 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_61')->first();
                        @endphp
                        @if ($gg_61)
                            <img src="{{ url('images/'.$gg_61->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_62 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_62')->first();
                        @endphp
                        @if ($gg_62)
                            <img src="{{ url('images/'.$gg_62->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_63 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_63')->first();
                        @endphp
                        @if ($gg_63)
                            <img src="{{ url('images/'.$gg_63->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_64 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_64')->first();
                        @endphp
                        @if ($gg_64)
                            <img src="{{ url('images/'.$gg_64->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_65 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_65')->first();
                        @endphp
                        @if ($gg_65)
                            <img src="{{ url('images/'.$gg_65->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>  
                </tr>
                <tr>
                    <td> </td><td> </td><td> </td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td> </td><td> </td><td> </td>
                </tr>
                <tr>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_85 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_85')->first();
                        @endphp
                        @if ($gg_85)
                            <img src="{{ url('images/'.$gg_85->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_84 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_84')->first();
                        @endphp
                        @if ($gg_84)
                            <img src="{{ url('images/'.$gg_84->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_83 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_83')->first();
                        @endphp
                        @if ($gg_83)
                            <img src="{{ url('images/'.$gg_83->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_82 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_82')->first();
                        @endphp
                        @if ($gg_82)
                            <img src="{{ url('images/'.$gg_82->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_81 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_81')->first();
                        @endphp
                        @if ($gg_81)
                            <img src="{{ url('images/'.$gg_81->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_71 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_71')->first();
                        @endphp
                        @if ($gg_71)
                            <img src="{{ url('images/'.$gg_71->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_72 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_72')->first();
                        @endphp
                        @if ($gg_72)
                            <img src="{{ url('images/'.$gg_72->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_73 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_73')->first();
                        @endphp
                        @if ($gg_73)
                            <img src="{{ url('images/'.$gg_73->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_74 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_74')->first();
                        @endphp
                        @if ($gg_74)
                            <img src="{{ url('images/'.$gg_74->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_75 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_75')->first();
                        @endphp
                        @if ($gg_75)
                            <img src="{{ url('images/'.$gg_75->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>
                <tr>
                    <td style="height: 5px;"> </td>
                </tr>
                <tr>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_48 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_48')->first();
                        @endphp
                        @if ($gg_48)
                            <img src="{{ url('images/'.$gg_48->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_47 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_47')->first();
                        @endphp
                        @if ($gg_47)
                            <img src="{{ url('images/'.$gg_47->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_46 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_46')->first();
                        @endphp
                        @if ($gg_46)
                            <img src="{{ url('images/'.$gg_46->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_45 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_45')->first();
                        @endphp
                        @if ($gg_45)
                            <img src="{{ url('images/'.$gg_45->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_44 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_44')->first();
                        @endphp
                        @if ($gg_44)
                            <img src="{{ url('images/'.$gg_44->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_43 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_43')->first();
                        @endphp
                        @if ($gg_43)
                            <img src="{{ url('images/'.$gg_43->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_42 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_42')->first();
                        @endphp
                        @if ($gg_42)
                            <img src="{{ url('images/'.$gg_42->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_41 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_41')->first();
                        @endphp
                        @if ($gg_41)
                            <img src="{{ url('images/'.$gg_41->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_31 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_31')->first();
                        @endphp
                        @if ($gg_31)
                            <img src="{{ url('images/'.$gg_31->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_32 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_32')->first();
                        @endphp
                        @if ($gg_32)
                            <img src="{{ url('images/'.$gg_32->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_anterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_33 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_33')->first();
                        @endphp
                        @if ($gg_33)
                            <img src="{{ url('images/'.$gg_33->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_34 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_34')->first();
                        @endphp
                        @if ($gg_34)
                            <img src="{{ url('images/'.$gg_34->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_35 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_35')->first();
                        @endphp
                        @if ($gg_35)
                            <img src="{{ url('images/'.$gg_35->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_36 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_36')->first();
                        @endphp
                        @if ($gg_36)
                            <img src="{{ url('images/'.$gg_36->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_37 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_37')->first();
                        @endphp
                        @if ($gg_37)
                            <img src="{{ url('images/'.$gg_37->value.'.png') }}" alt="">
                        @endif
                    </td>
                    <td class="gigi_posterior" style="height: 25px; width: 25px;">
                        @php
                            $gg_38 = collect($listPemeriksaanOdontogram)->where('gg_xx', 'gg_38')->first();
                        @endphp
                        @if ($gg_38)
                            <img src="{{ url('images/'.$gg_38->value.'.png') }}" alt="">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>8</td><td>7</td><td>6</td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td>
                </tr>
            </table>
            <table style="margin: 0 auto; width: 450px;">
                <tr>
                  <td><img src="{{ url('images/Tanggal.png') }}"></td>
                  <td> = Hilang</td>
                  <td><img src="{{ url('images/Karies.png') }}"></td>
                  <td> = Karies</td>
                </tr>
                <tr>
                  <td style="height: 5px;"> </td>
                </tr>
                <tr>
                  <td><img src="{{ url('images/Akar.png') }}"></td>
                  <td> = Sisa Akar</td>
                  <td><img src="{{ url('images/Tumpat.png') }}"></td>
                  <td> = Tumpatan</td>
                </tr>
              </table>
        </div>
        <form wire:submit.prevent='save'>
            <div class="row">
                <div wire:ignore class="col-md-6">
                    <div class="form-group">
                        <label> Pilih Gigi</label>
                        <select name="gg_xx" id="gg_xx" wire:model.defer='gigi' class="form-control formgigi" required>
                            <option value="" >------------</option>
                            <option value="gg_18">Gigi 18</option>
                            <option value="gg_17">Gigi 17</option>
                            <option value="gg_16">Gigi 16</option>
                            <option value="gg_15">Gigi 15</option>
                            <option value="gg_14">Gigi 14</option>
                            <option value="gg_13">Gigi 13</option>
                            <option value="gg_12">Gigi 12</option>
                            <option value="gg_11">Gigi 11</option>
                            <option value="gg_21">Gigi 21</option>
                            <option value="gg_22">Gigi 22</option>
                            <option value="gg_23">Gigi 23</option>
                            <option value="gg_24">Gigi 24</option>
                            <option value="gg_25">Gigi 25</option>
                            <option value="gg_26">Gigi 26</option>
                            <option value="gg_27">Gigi 27</option>
                            <option value="gg_28">Gigi 28</option>
                            <option value="gg_38">Gigi 38</option>
                            <option value="gg_37">Gigi 37</option>
                            <option value="gg_36">Gigi 36</option>
                            <option value="gg_35">Gigi 35</option>
                            <option value="gg_34">Gigi 34</option>
                            <option value="gg_33">Gigi 33</option>
                            <option value="gg_32">Gigi 32</option>
                            <option value="gg_41">Gigi 31</option>
                            <option value="gg_41">Gigi 41</option>
                            <option value="gg_42">Gigi 42</option>
                            <option value="gg_43">Gigi 43</option>
                            <option value="gg_44">Gigi 44</option>
                            <option value="gg_45">Gigi 45</option>
                            <option value="gg_46">Gigi 46</option>
                            <option value="gg_47">Gigi 47</option>
                            <option value="gg_48">Gigi 48</option>
                            <option value="gg_55">Gigi 55</option>
                            <option value="gg_54">Gigi 54</option>
                            <option value="gg_53">Gigi 53</option>
                            <option value="gg_52">Gigi 52</option>
                            <option value="gg_51">Gigi 51</option>
                            <option value="gg_61">Gigi 61</option>
                            <option value="gg_62">Gigi 62</option>
                            <option value="gg_63">Gigi 63</option>
                            <option value="gg_64">Gigi 64</option>
                            <option value="gg_65">Gigi 65</option>
                            <option value="gg_75">Gigi 75</option>
                            <option value="gg_74">Gigi 74</option>
                            <option value="gg_73">Gigi 73</option>
                            <option value="gg_72">Gigi 72</option>
                            <option value="gg_71">Gigi 71</option>
                            <option value="gg_81">Gigi 81</option>
                            <option value="gg_82">Gigi 82</option>
                            <option value="gg_83">Gigi 83</option>
                            <option value="gg_84">Gigi 84</option>
                            <option value="gg_85">Gigi 85</option>
                        </select>
                        <div class="invalid-feedback">
                            @error('gigi') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div wire:ignore class="form-group">
                        <label for="penyakit">Hasil Pemeriksaan</label>
                        <select class="form-control formpemeriksaan" wire:model.defer='penyakit' id="penyakit" required>
                            <option value="">------------</option>
                            <option value="Tanggal">Tanggal</option>
                            <option value="Karies">Karies</option>
                            <option value="Akar">Sisa Akar</option>
                            <option value="Tumpat">Tumpatan</option>
                            <option value="Fraktur Mahkota">Fraktur Mahkota</option>
                        </select>
                        <div class="invalid-feedback">
                            @error('penyakit') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea wire:model.defer="catatan" class="form-control" id="catatan" rows="3"></textarea>
                @error('catatan') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="d-flex flex-row-reverse pb-3">
                <button class="btn btn-primary ml-1" type="submit" > Simpan </button>
            </div>
        </form>
        <div class="callout callout-info">
            <h5>Riwayat Pemeriksaan Odontogram</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Gigi</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($listPemeriksaanOdontogram as $item)
                            <tr>
                                <td>{{ $item->tgl_perawatan }}  {{ $item->jam_rawat }}</td>
                                <td>{{ $item->gg_xx }}</td>
                                <td>{{ $item->value }}</td>
                                <td>{{ $item->catatan }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm" wire:click="delete('{{ $item->gg_xx }}', '{{ $item->tgl_perawatan }}')">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Pemeriksaan Odontogram Kosong</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('css')
    <style>
        .table-odontogram {
            width: 100%;
            overflow-y: auto;
            _overflow: auto;
            margin: 0 0 1em;
            padding-right: 0px;
        }
        .gigi_posterior{
            background: url("../images/posterior.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        .gigi_anterior{
            background: url("../images/anterior.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        td.name { font-weight: bold; font-size: 14px; }
        input.odont_input, .odont_color {
            width: 25px;
            height: 25px;
            padding: 6px 6px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }


        /* Form styles */
        div.form-container { margin: 10px; padding: 5px; background-color: #FFF; }

        p.legend { margin-bottom: 1em; }
        p.legend em { color: #C00; font-style: normal; }

        div.form-container div.controlset {display: block; float:left; width: 100%; padding: 0.25em 0;}

        div.form-container div.controlset label,
        div.form-container div.controlset input,
        div.form-container div.controlset div { display: inline; float: left; }

        div.form-container div.controlset label { width: 100px;}

        div.color_picker {
            height: 25px;
            width: 25px;
            padding: 0 !important;
            border: 1px solid #ccc;
            background: url(arrow.gif) no-repeat top right;
            cursor: pointer;
            line-height: 16px;
        }

        div#color_selector {
            width: 106px;
            position: absolute;
            border: 1px solid #598FEF;
            background-color: #EFEFEF;
            padding: 2px;
        }
        div#color_custom {width: 100%; float:left }
        div#color_custom label {font-size: 95%; color: #2F2F2F; margin: 5px 2px; width: 25%}
        div#color_custom input {margin: 5px 2px; padding: 0; font-size: 95%; border: 1px solid #000; width: 65%; }

        div.color_swatch {
            height: 20px;
            width: 20px;
            border: 1px solid #000;
            margin: 2px;
            float: left;
            cursor: pointer;
            line-height: 12px;
        }
        .was-validated .custom-select:invalid + .select2 .select2-selection{
            border-color: #dc3545!important;
        }
        .was-validated .custom-select:valid + .select2 .select2-selection{
            border-color: #28a745!important;
        }
        *:focus{
        outline:0px;
        }
    </style>
@endpush

@section('js')
    <script>
        $('.formgigi').select2({
            placeholder: 'Pilih Gigi',
            minimumResultsForSearch: Infinity
        });
        
        $('.formpemeriksaan').select2({
            placeholder: 'Pilih hasil pemeriksaan',
            minimumResultsForSearch: Infinity
        });

        $('.formgigi').on('change', function (e) {
            let data = $(this).val();
            @this.set('gigi', data);
        });

        $('.formpemeriksaan').on('change', function (e) {
            let data = $(this).val();
            @this.set('penyakit', data);
        });

        window.addEventListener('swal:odontogram',function(e){
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:odontogram:confirm',function(e){
            Swal.fire({
                title: e.detail.title,
                text: e.detail.text,
                icon: e.detail.icon,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: e.detail.confirmButtonText,
                cancelButtonText: e.detail.cancelButtonText,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit(e.detail.function, e.detail.params[0]);
                }
            });
        });

        window.livewire.on('select2', () => {
            console.log('select2');
            $('.formgigi').select2({
                placeholder: 'Pilih Gigi'
            });
        });
    </script>
@endsection
