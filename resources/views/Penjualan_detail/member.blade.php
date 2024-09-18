<div class="modal fade" id="modal-member" tabindex="-1" role="dialog" aria-labelledby="modal-member">
  <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="Pilih member">Pilih Member</h4>
            </div>
            <div class="modal-body">
              <table class="table table-striped table-bordered table-member">
                <thead>
                    <th width="5%">No</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th><i class="fa fa-cog"></i></th>
                </thead>
                <tbody>
                    @foreach ($member as $key => $item )
                        <tr>
                            <td>{{ $key+1 }}</td> 
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->telepon }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>
                            <a href="#" class="btn btn-primary btn-xs btn-flat"
                                    onclick="pilihMember('{{ $item->id_member }}', '{{ $item->kode_member }}')">
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