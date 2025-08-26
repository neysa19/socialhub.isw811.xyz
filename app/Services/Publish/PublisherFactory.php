return match($provider) {
    'twitter'  => new TwitterV2Publisher,   // << usa el nuevo
    'facebook' => new FacebookPublisher,
    'instagram'=> new InstagramPublisher,
    default    => throw new InvalidArgumentException('Proveedor no soportado: '.$provider)
};