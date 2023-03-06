<x-user :sharedData="$sharedData" pageTitle="{{$sharedData['username']}}'s following">
   @include('profile-following-only')
  </x-user>