<?php

namespace App\Models;

use App\Models\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use League\OAuth2\Server\CryptKey;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Laravel\Passport\Bridge\AccessToken as BaseToken;
use Jenssegers\Agent\Agent;

class AccessToken extends BaseToken {

	private $privateKey;

	/**
	 * Generate a string representation from the access token
	 */
	public function __toString() {
		return (string) $this->convertToJWT( $this->privateKey );
	}

	/**
	 * Set the private key used to encrypt this access token.
	 */
	public function setPrivateKey( CryptKey $privateKey ) {
		$this->privateKey = $privateKey;
	}

	public function convertToJWT( CryptKey $privateKey ) {
        $builder = new Builder();
        $expiryTimestamp = $this->getExpiryDateTime()->getTimestamp();

        $agent = new Agent();
        if ($agent->isAndroidOS()) {
            $expiryTimestamp = now()->addHours(1)->getTimestamp();
        }

		$builder->permittedFor( $this->getClient()->getIdentifier() )
		        ->identifiedBy( $this->getIdentifier(), true )
		        ->issuedAt( time() )
		        ->canOnlyBeUsedAfter( time() )
		        ->expiresAt( $expiryTimestamp )
                ->relatedTo( $this->getUserIdentifier() )
		        ->withClaim( 'scopes', $this->getScopes() );

		if ( $user = User::find( $this->getUserIdentifier() ) ) {
            		// Include additional user claims for user
            $builder
                ->withClaim( 'user_id', $user->id )
                ->withClaim( 'name', $user->name )
                ->withClaim( 'email', $user->email)
                ->withClaim( 'role', $user->role);

		}

		return $builder
			->getToken( new Sha256(), new Key( $privateKey->getKeyPath(), $privateKey->getPassPhrase() ) );
	}
}
