<div class="modal fade" id="modal-produk" tabindex="-1" role="dialog" aria-labelledby="modal-produk">
  <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Produk</h4>
            </div>
            <div class="modal-body">
              <table class="table table-striped table-bordered table-produk">
                <thead>
                    <th width="5%">No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Harga Jual</th>
                    <th><i class="fa fa-cog"></i></th>
                </thead>
                <tbody>
                    @foreach ($product as $key => $item )
                        <tr>
                            <td width="5%">{{ $key+1 }}</td> 
                            <td><div class="span"><div class="label label-success">{{ $item->kode_produk }}</div></div></td>
                            <td>{{ $item->nama_produk }}</td>
                            <td>Rp.{{ format_uang($item->harga_jual) }}</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-xs btn-flat"
                                    onclick="pilihProduk('{{ $item->id_produk }}', '{{ $item->kode_produk }}')">
                                    <i class="fa fa-check-circle">
                                        Pilih
                                    </i>
                                </a>
                            </td>
                        </tr>
                        <!-- key digunakan untuk penomoran pada modal nya -->
                    @endforeach
                </tbody>
              </table>
            </div>
        </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->