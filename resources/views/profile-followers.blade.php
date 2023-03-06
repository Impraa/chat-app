<x-user :sharedData="$sharedData" pageTitle="{{$sharedData['username']}}'s followers">
    @include('profile-followers-only')
  </x-user>