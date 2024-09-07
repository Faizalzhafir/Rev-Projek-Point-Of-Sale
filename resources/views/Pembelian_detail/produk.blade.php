<div class="modal fade" id="modal-supplier" tabindex="-1" role="dialog" aria-labelledby="modal-supplier">
  <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="Pilih Supplier">Pilih Supplier</h4>
            </div>
            <div class="modal-body">
              <table class="table table-striped table-bordered">
                <thead>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Harga Beli</th>
                    <th><i class="fa fa-cog"></i></th>
                </thead>
                <tbody>
                    @foreach ($product as $key => $item )
                        <tr>
                            <td>{{ $key+1 }}</td> 
                            <td>{{ $item->kode_produk }}</td>
                            <td>{{ $item->nama_produk }}</td>
                            <td>{{ $item->harga_beli }}</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-xs btn-flat">
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