<x-user :sharedData="$sharedData" pageTitle="{{$sharedData['username']}}'s following">
    <div class="list-group">
      @foreach ($following as $follow)
      <a href="/profile/{{$follow->userFollowed->username}}" class="list-group-item list-group-item-action">
          <img class="avatar-tiny" src="{{$follow->userFollowed->avatar}}" />
          {{$follow->userFollowed->username}}
        </a>
      @endforeach
  </div>
  </x-user>