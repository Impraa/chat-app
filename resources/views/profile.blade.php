<x-user :sharedData="$sharedData" pageTitle="{{$sharedData['username']}}'s Profile">
  <div class="list-group">
    @foreach ($posts as $post)
    <x-post :post="$post" hideAuthor="true"/>
    @endforeach
</div>
</x-user>