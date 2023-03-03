<x-user :sharedData="$sharedData" pageTitle="{{$sharedData['username']}}'s followers">
    <div class="list-group">
      @foreach ($followers as $follower)
      <a href="/profile/{{$follower->userFollowing->username}}" class="list-group-item list-group-item-action">
          <img class="avatar-tiny" src="{{$follower->userFollowing->avatar}}" />
         {{$follower->userFollowing->username}}
        </a>
      @endforeach
  </div>
  </x-user>