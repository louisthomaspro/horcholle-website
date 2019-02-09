

@extends('admin.layouts.app')
@section('pageTitle', $pageTitle)

@section('content')
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-10">
        <div class="card">
          <div class="card-header">Dashboard</div>
          <div class="card-body">
            <ul class="list-group">
               <li class="list-group-item d-flex justify-content-between align-items-center"><div><button id="syncRealisations" type="button" class="btn btn-primary mr-2">Lancer la synchronisation</button>Synchroniser les réalisations</div><small class="text-muted">.</small></li>
              <li class="list-group-item d-flex justify-content-between align-items-center"><div><button id="syncPictures" type="button" class="btn btn-primary mr-2">Lancer la synchronisation</button>Synchroniser les images (page réalisation exclue)</div><small class="text-muted">.</small></li>
            </ul>
            
            <br>
            <p>La synchronisation peut prendre plusieurs minutes...</p>
            <a style="font-size:18px;" target="_blank" href="https://drive.google.com/drive/folders/1JLnq0P7xGRu_bTghR_exJplHeT8RxAxo?usp=sharing">Drive</a>

          </div>
        </div>

      </div>
    </div>
  </div>
@endsection

@section('script')
<script>

  $(document).ready(function(){

    $('#syncPictures').click(function(e){
          e.preventDefault();

      $(this).attr("disabled", true);
      $(this).html('Synchronisation en cours...');

        $.ajax({
            method: 'get',
            url: "{{ route('admin.syncPictures') }}",

            success: function(response) {
                console.log("success");
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.log("error");
                console.log(error);
                console.log(xhr.responseText);
                
            },
            complete: function() {
              $('#syncPictures').attr("disabled", false);
              $('#syncPictures').html('Lancer la synchronisation');
            }
        });


    });


    $('#syncRealisations').click(function(e){
          e.preventDefault();

      $(this).attr("disabled", true);
      $(this).html('Synchronisation en cours...');
      

        $.ajax({
            method: 'get',
            url: "{{ route('admin.syncRealisations') }}",
            data:'_token = <?php echo csrf_token() ?>',

            success: function(response) {
                console.log("success");
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.log("error");
                console.log(error);
                console.log(xhr.responseText);
                
            },
            complete: function() {
              $('#syncRealisations').attr("disabled", false);
              $('#syncRealisations').html('Lancer la synchronisation');
            }
        });

    });



});

 

  
</script>
@endsection