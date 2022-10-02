@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1>Referrals</h1>
                </div>

                <div class="panel-body">
                    <div>@include('partials.filterReferrals') @include('partials.createReferralButton')</div>
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    <table class="table table-responsive">
                        <tr>
                            <th>Country</th>
                            <th>Reference No</th>
                            <th>Organisation</th>
                            <th>Province</th>
                            <th>District</th>
                            <th>City</th>
                            <th>Street Address</th>
                            <th>Gps Location</th>
                            <th>Facility Name</th>
                            <th>Facility Type</th>
                            <th>Provider Name</th>
                            <th>Position</th>
                            <th>Phone</th>
                            <th>eMail</th>
                            <th>Website</th>
                            <th>Pills Available</th>
                            <th>Code To Use</th>
                            <th>Type of Service</th>
                            <th>Note</th>
                            <th>Womens Evaluation</th>
                            <th>Comments</th>
                        </tr>
                        <script>
                            refComments = {};
                            currentUser = {};
                            currentUser.name = "{{Auth::user()->name}}";
                        </script>
                        @foreach($referrals as $referral)
                        <script>
                            refID = 0;
                            refComments["{{ '_'.$referral->reference_no }}"] = [
                                @foreach($referral->comments()->get()->all() as $comment)
                                {{"{"}}
                                id : {{$comment->id}},
                                name : "{{(\App\User::find($comment->user_id)->first()->name)}}",
                                comment : "{{$comment->comment}}"
                                {{"},"}}
                                @endforeach
                            ];
                        </script>
                        <tr>
                            <td>{{ $referral->country }} </td>
                            <td>{{ $referral->reference_no }} </td>
                            <td>{{ $referral->organisation }} </td>
                            <td>{{ $referral->province }} </td>
                            <td>{{ $referral->district }} </td>
                            <td>{{ $referral->city }} </td>
                            <td>{{ $referral->street_address }} </td>
                            <td>{{ $referral->gps_location }} </td>
                            <td>{{ $referral->facility_name }} </td>
                            <td>{{ $referral->facility_type }} </td>
                            <td>{{ $referral->provider_name }} </td>
                            <td>{{ $referral->position }} </td>
                            <td>{{ $referral->phone }} </td>
                            <td>{{ $referral->email }} </td>
                            <td>{{ $referral->website }} </td>
                            <td>{{ $referral->pills_available }} </td>
                            <td>{{ $referral->code_to_use }} </td>
                            <td>{{ $referral->type_of_service }} </td>
                            <td>{{ $referral->note }} </td>
                            <td>{{ $referral->womens_evaluation }} </td>
                            <td class="expandable">
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#showCommentModal" id="show_{{ $referral->reference_no }}" onclick="openShowCommentModal('{{ $referral->reference_no }}')">Open Comments</button>

                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>

                <div class="panel-footer">
                    {{ $referrals->links() }}
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Show-Comment-Modal -->
<div class="modal fade" id="showCommentModal" tabindex="-1" role="dialog" aria-labelledby="showCommentModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="show-comment-title">Comments on referral #<span class="show-comment-title-span"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#commentModal" onclick="openAddCommentModal()">Add</button>
                <h3>Comments</h3>
                <ul id="comments-ul">
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" id="closeShowCommentModal" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add-Comment-Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add comment to Referral #<span id="comment-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form" id="comment-form" action="/add-comment" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="referral_id" value="">
                    <div class="form-group">
                        <label for="comment">Your comment </label>
                        <input type="text" id="comment" name="comment" class="form-control" placeholder="Comment.." />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" data-dismiss="modal" onclick="saveComment()">Save</button>
            </div>
        </div>
    </div>
</div>


<script>
    /**
     * Prepare comment create modal
     */
    const openAddCommentModal = () => {
        $("#comment-title").text(refID);
        $("#comment").val("");
    };
    /**
     * Prepare comment view modal
     */
    const openShowCommentModal = (refId) => {
        $(".show-comment-title-span")[0].innerHTML = refId;
        refID = refId;
        $("#comments-ul").html(' <span>Loading comments...</span>')
        let html = "";
        let comments = refComments[('_'+refId)];
        if(comments.length >=1){
            comments.forEach((e, i)=> {
                if(!e.hasOwnProperty("deleted")) html+=`<li id="comment_${e.id}">${e.comment} <small style="float:right;"> <a  data-index="${e.id}" href="javascript:deleteComment(${e.id}, this)" class="text text-red">Delete</a> </small> <small style="float:right;">by ${e.name}</small> </li>`
            })
            $("#comments-ul").html(html);
        }
        else  {
            $("#comments-ul").html("<span>No comments yet, use the button above to add a new comment</span>");
        }
        refID = refId;
    };

    /**
     * Store comment via AJAX request
     */
    const saveComment = () => {
        const csrf = $("meta[name=csrf-token]").attr("content");
        $.post("{{ route('add-comment') }}", {
            _token: csrf,
            user_id: "{{Auth::user()->id}}",
            referral_id: refID,
            user_id: "{{ Auth::User()->id }}",
            comment: $("#comment").val()
        }, (response) => {
            if(response.statusCode == 1){
                toastr.success(response.message ?? "OK!");
                refComments['_'+refID].push(response.data);
                refreshComment();
            }
            
        });
    };

    /**
     * Delete comment by `id` via AJAx request
     */
    const deleteComment = (id) => {
        let url = "/delete-comment/"+id;
        $.getJSON(url, (data)=>{
            if(data.statusCode == 1){
                toastr.success(data.message ?? "OK!");
                let comment = refComments['_'+refID];
                let commentId = 0;
                comment.forEach((e, i) => {
                    if(e.id == id) commentId = i;
                })
                $("#comment_"+id).remove();
                comment[commentId].deleted = true;
            }
            else {
                toastr.error(data.message ?? "OK!");
            }
            refreshComment();
        })
    };

    /**
     * Close modals and reopen them for fresh data
     */
    const refreshComment = () => {
        openShowCommentModal(refID);
    };
</script>
@endsection