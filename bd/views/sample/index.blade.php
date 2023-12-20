<?php
  $INTERVAL = 10;

  if (request()->isMethod('post')) {
    $message = request()->input('message');
    if (!$message) {
      $query = request()->query();
      $query['error'] = 'No message'; 
      header('Location: '.request()->url().'?'.Arr::query($query));
      return;
    }

    if (!\Unsta\FloodControl::isAllowed(request()->url().'::message', 1, $INTERVAL)) {
      $query = request()->query();
      $query['error'] = "You can only use it once every $INTERVAL seconds.";
      header('Location: '.request()->url().'?'.Arr::query($query));
      return;
    }

    \Unsta\FloodControl::register(request()->url().'::message', $INTERVAL);
    // request()->session()->regenerateToken();

    $uuid = (string)Str::uuid();
    Cache::put(request()->url().$uuid, $message, 30);
    $query = request()->query();
    $query['uuid'] = $uuid; 
    header('Location: '.request()->url().'?'.Arr::query($query));
    return;
  }

  $message = false;
  if (request()->has('uuid')) {
    $message = Cache::pull(request()->url().request()->input('uuid'));
    if (!$message) {
      $query = request()->query();
      unset($query['uuid']);
      $query['error'] = "This message will self-destruct."; 
      header('Location: '.request()->url().'?'.Arr::query($query));
      return;
    }
  }

  $error = request()->query('error');
?>

@props([
  'uniq_class' => '_SC_' . uniqid(),
])

<x-sample.html>
  <x-slot:title>
    {{ HQ::getenv('CCC::APP_NAME') }} 
  </x-slot>  

  <x-slot:header>
    <link rel="stylesheet" href="?css_route=sample.style">
  </x-slot>  

  {{-- slot --}}
  <div class="{{$uniq_class}} wrapper">
    <div class="title">
      {{ HQ::getenv('CCC::APP_NAME') }} 
    </div>  

    @if(!$error)
      @if($message === false)
        <div>
          <form action="{{ '?'.Arr::query(request()->query()) }}" method="POST">
            @csrf
            <textarea rows="6" name="message"></textarea>
            <button type="submit">
              SEND MESSAGE
            </button>
          </form>
        </div>

        <div>
          Your Query(JSON) =>
          <pre class="color-box">{{ json_encode(request()->query(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
        </div>
        
        <div>
          Your Query(YAML) =>
          <pre class="color-box">{{ Symfony\Component\Yaml\Yaml::dump(request()->query()) }}</pre>
        </div>
        
        <div>
          Markdown =>
          <div class="color-box">
            {!! Compilers::markdown('sample.test', ['test' => 2]) !!}
          </div>
        </div>
      @else
        <div>
          Your Message =>
          <pre class="color-box">{{ $message }}</pre>
          <div>
            Please go back.
          </div>
        </div>
      @endif
    @else
      Error: 
      <div>
        {{ $error }}
      </div>
      <div>
        Please go back.
      </div>
    @endif
  </div>

{{-- Dynamic SCSS --}}
@push('style')
// <style>

// You can use SCSS style!!

.{{ $uniq_class }} {
  .color-box {
    border: yellow 1px solid;
    padding: 1rem;
  }

  form {
    textarea {
      width: 100%;
    }
  }
}

// </style>
@endpush  

</x-sample.html>
