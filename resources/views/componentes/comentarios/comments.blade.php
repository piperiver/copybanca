<div class="modal-dialog">
    <form data-url="/guardarComentario/{{$type}}/{{$id}}" id="comment-form" style="padding: 15px">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        </div>
        <div class="modal-body">

                <div class="portlet box main-color sinMarginBottom">
                    <div style="text-align: center; padding: 0px !important; min-height: 0px!important;" class="portlet-title" >
                        <strong style="font-size: 1.4em">COMENTARIOS</strong>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="comments-list" style="max-height: 250px; overflow: auto;padding: 15px;">
                                        @foreach($comments as $comment)
                                            <p style="text-align: justify">
                                                {{$comment->comment}}
                                                <br>
                                                <small style="color: blue; font-weight: bold" class="media-heading user_name">Por {{$comment->commented->nombre}} el {{$comment->created_at}}</small>
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="form-group">
                                <label for="comment">Comentario:</label>
                                <textarea maxlength="500" name="comment" style="resize: none" class="form-control" rows="5" cols="1" id="commentBody"></textarea>
                            </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Guardar comentario</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
        </div>
    </div>
    </form>

</div>