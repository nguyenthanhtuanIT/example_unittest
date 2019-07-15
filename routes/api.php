<?php
Route::group(['prefix' => 'v1'], function () {
    Route::post('auth/google', 'Auth\AuthGoogleController@login');
    Route::get('list_films', 'FilmController@listFilmToVote');
    Route::get('get_blogs', 'BlogController@getBlog');
    Route::resource('blogs', 'BlogController')->only('index', 'show');
    Route::get('status_vote', 'VoteController@showStatusVote');
    Route::post('info_vote', 'VoteController@infoVotes');
    Route::post('film_to_register', 'FilmController@getFilmToRegister');
    Route::resource('statisticals', 'StatisticalController');
    Route::get('user_comment/{blogId}', 'CommentController@getComments');
    Route::get('amount_vote_films/{voteId}', 'StatisticalController@getAmountVote');
    Route::post('search_blog', 'BlogController@searchBlogByTitle');
    Route::post('update_status_chair', 'ChairController@updateStatusChair');
    //Route::group(['middleware' => ['auth:api']], function () {
    //user information
    Route::get('me', 'UserController@me');
    Route::post('auth/logout', 'Auth\AuthController@logout');
    Route::post('ticket_of_user', 'ChooseChairController@ticketOfUser');
    //user choose chairs
    Route::resource('choose_chairs', 'ChooseChairController');
    //user voting
    Route::resource('vote_details', 'VoteDetailController');
    // //check voted
    Route::post('check_voted', 'VoteDetailController@checkVoted');
    Route::resource('registers', 'RegisterController');
    //check register
    Route::post('check_register', 'RegisterController@checkRegistered');
    Route::post('un_register', 'RegisterController@unRegister');
    Route::post('guest_refuse', 'RegisterController@guestRefuses');
    Route::post('check_user_choose_chair', 'ChooseChairController@checkUserChoosed'); //
    Route::post('re_choose_chair', 'ChooseChairController@reChooses');
    Route::post('list_users', 'UserController@listUsers');
    Route::post('un_voted', 'VoteDetailController@unVoted');
    Route::resource('statisticals', 'StatisticalController');
    Route::resource('comments', 'CommentController')->only('store', 'update', 'destroy');
    Route::post('agree', 'RegisterController@userAgree');

    // Route::group(['prefix' => 'admin', 'middleware' => 'checkroles'], function () {
    Route::resource('users', 'UserController');
    Route::resource('votes', 'VoteController');
    Route::resource('films', 'FilmController');
    Route::resource('cinemas', 'CinemaController');
    Route::resource('rooms', 'RoomController');
    Route::resource('diagrams', 'DiagramController');
    Route::get('choose_chairs', 'ChooseChairController@index');
    Route::delete('del_choose_chairs/{voteId}', 'ChooseChairController@deleteAll');
    Route::delete('del_chairs/{voteId}', 'ChairController@deleteAll');
    Route::resource('statisticals', 'StatisticalController');
    Route::delete('del_statisticals/{voteId}', 'StatisticalController@deleteAll');
    Route::resource('vote_details', 'VoteDetailController');
    Route::delete('del_votedetails/{voteId}', 'VoteDetailController@deleteAll');
    Route::delete('delete_all/{roomId}', 'DiagramController@deleteAll');
    Route::delete('del_all/{voteId}', 'RandomController@deleteAll');
    Route::resource('vote_details', 'VoteDetailController')->only('index', 'destroy');
    Route::resource('registers', 'RegisterController')->only('index', 'destroy');
    Route::post('rand', 'ChooseChairController@randChairs');
    Route::get('info_detail/{voteId}', 'StatisticalController@getInfoByVote');
    Route::get('info_list_vote', 'StatisticalController@getInfo');
    Route::post('search_by_room', 'DiagramController@searchByRoom');
    Route::get('excel/{voteId}', 'RegisterController@Export');
    Route::resource('rands', 'RandomController');
    Route::resource('blogs', 'BlogController');
    Route::resource('chairs', 'ChairController');
    Route::resource('comments', 'CommentController');
    Route::resource('choose_chairs', 'ChooseChairController')->only(['index', 'update', 'destroy']);
    Route::get('chair_rand_by_vote/{voteId}', 'RandomController@getChairsByVote');
    Route::get('chair_by_vote/{voteId}', 'ChairController@getDiagramChairByVote');
    //     });
    // });
});
