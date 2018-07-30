
<!-- Esta pagina contiene el footer, en el cual se encuentran librerias javascript necesarias para correr el tema y la ventana modal del video-->
<!-- inicio modal -->

      <!-- Modal -->
      <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <video class="Mvideo" controls loop="loop" muted="true">
                    <source src="{{ asset('video/conectate.mp4') }}" type="video/mp4">
                </video>
            </div>
          </div>
        </div>
      <!-- fin modal -->

<script src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.js') }}"></script>
<script src="{{ asset('js/carousel-swipe.js') }}"></script>
<script src="{{ asset('js/controller.js') }}"></script>
