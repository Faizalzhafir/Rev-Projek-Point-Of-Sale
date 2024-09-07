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
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th><i class="fa fa-cog"></i></th>
                </thead>
                <tbody>
                    @foreach ($supplier as $key => $item )
                        <tr>
                            <td>{{ $key+1 }}</td> 
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->telepon }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>
                                <a href="{{ route('pembelian.create', $item->id_supplier) }}" class="btn btn-primary btn-xs btn-flat">
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