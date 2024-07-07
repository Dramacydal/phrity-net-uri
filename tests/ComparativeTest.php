<?php

/**
 * Tests for Net\Uri class.
 * @package Phrity > Net > Uri
 */

declare(strict_types=1);

namespace Phrity\Net;

use GuzzleHttp\Psr7\Uri as Guzzle;
use Laminas\Diactoros\Uri as Lamias;
use League\Uri\Uri as League;
use Nyholm\Psr7\Uri as Nyholm;
use Slim\Psr7\Factory\UriFactory as SlimFactory;
use Slim\Psr7\Uri as Slim;
use PHPUnit\Framework\TestCase;
use Phrity\Net\Uri as Phrity;

/**
 * Compares result of some common URI implementations.
 */
class ComparativeTest extends TestCase
{
    private string $gendelims = ':/?#[]@';
    private string $subdelims = '!$&\'()*+,;=';
    private string $unreserved  = 'a0-._~';
    private string $pct = '%20';
    private string $nonascii = 'ö 😇必';

    public function testUserInfo(): void
    {
        // Check gen-delims
        $user = $pass = $this->gendelims;
        $phrity = $this->getPhrity()->withUserInfo($user, $pass);
        $guzzle = $this->getGuzzle()->withUserInfo($user, $pass);
        $lamias = $this->getLamias()->withUserInfo($user, $pass);
        $league = $this->getLeague()->withUserInfo($user, $pass);
        $nyholm = $this->getNyholm()->withUserInfo($user, $pass);
        $slim = $this->getSlim()->withUserInfo($user, $pass);

        // Guzzle, Lamias, Nyholm and Slim do encode gen-delims
        $this->assertEquals($guzzle->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($lamias->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($nyholm->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($slim->getUserInfo(), $phrity->getUserInfo());
        // League do not encode ":" in password part (only in user part)
        $this->assertNotEquals($league->getUserInfo(), $phrity->getUserInfo());

        // Check sub-delims
        $user = $pass = $this->subdelims;
        $phrity = $this->getPhrity()->withUserInfo($user, $pass);
        $guzzle = $this->getGuzzle()->withUserInfo($user, $pass);
        $lamias = $this->getLamias()->withUserInfo($user, $pass);
        $league = $this->getLeague()->withUserInfo($user, $pass);
        $nyholm = $this->getNyholm()->withUserInfo($user, $pass);
        $slim = $this->getSlim()->withUserInfo($user, $pass);

        // Guzzle, Lamias, League and Slim do not encode sub-delims
        $this->assertEquals($guzzle->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($lamias->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($league->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($slim->getUserInfo(), $phrity->getUserInfo());
        // Nyholm do encode sub-delims
        $this->assertNotEquals($nyholm->getUserInfo(), $phrity->getUserInfo());

        // Check unreserved
        $user = $pass = $this->unreserved;
        $phrity = $this->getPhrity()->withUserInfo($user, $pass);
        $guzzle = $this->getGuzzle()->withUserInfo($user, $pass);
        $lamias = $this->getLamias()->withUserInfo($user, $pass);
        $league = $this->getLeague()->withUserInfo($user, $pass);
        $nyholm = $this->getNyholm()->withUserInfo($user, $pass);
        $slim = $this->getSlim()->withUserInfo($user, $pass);

        // Guzzle, Lamias, League, Nyholm and Slim do not encode unreserved
        $this->assertEquals($guzzle->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($lamias->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($league->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($nyholm->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($slim->getUserInfo(), $phrity->getUserInfo());

        // Check pct
        $user = $pass = $this->pct;
        $phrity = $this->getPhrity()->withUserInfo($user, $pass);
        $guzzle = $this->getGuzzle()->withUserInfo($user, $pass);
        $lamias = $this->getLamias()->withUserInfo($user, $pass);
        $league = $this->getLeague()->withUserInfo($user, $pass);
        $nyholm = $this->getNyholm()->withUserInfo($user, $pass);
        $slim = $this->getSlim()->withUserInfo($user, $pass);

        // Guzzle, Lamias, League, Nyholm and Slim do not encode unreserved
        $this->assertEquals($guzzle->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($lamias->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($league->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($nyholm->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($slim->getUserInfo(), $phrity->getUserInfo());

        // Check non-ascii
        $user = $pass = $this->nonascii;
        $phrity = $this->getPhrity()->withUserInfo($user, $pass);
        $guzzle = $this->getGuzzle()->withUserInfo($user, $pass);
        $lamias = $this->getLamias()->withUserInfo($user, $pass);
        $league = $this->getLeague()->withUserInfo($user, $pass);
        $nyholm = $this->getNyholm()->withUserInfo($user, $pass);
        $slim = $this->getSlim()->withUserInfo($user, $pass);

        // Guzzle and League do encode all non-ascii
        $this->assertEquals($guzzle->getUserInfo(), $phrity->getUserInfo());
        $this->assertEquals($league->getUserInfo(), $phrity->getUserInfo());
        // Lamias and Slim do encode non-letter non-ascii
        $this->assertEquals($lamias->getUserInfo(), $phrity->getUserInfo(Phrity::URI_ENCODE));
        $this->assertEquals($slim->getUserInfo(), $phrity->getUserInfo(Phrity::URI_ENCODE));
        // Nyholm do not encode non-ascii
        $this->assertEquals($nyholm->getUserInfo(), $phrity->getUserInfo(Phrity::URI_DECODE));
    }

    public function testPath(): void
    {
        // Check gen-delims
        $path = "/{$this->gendelims}";
        $phrity = $this->getPhrity()->withPath($path);
        $guzzle = $this->getGuzzle()->withPath($path);
        $league = $this->getLeague()->withPath($path);
        $nyholm = $this->getNyholm()->withPath($path);
        $slim = $this->getSlim()->withPath($path);
        // Lamias do not allow gen-delims in path

        // Guzzle, League, Nyholm and Slim do encode gen-delims
        $this->assertEquals($guzzle->getPath(), $phrity->getPath());
        $this->assertEquals($league->getPath(), $phrity->getPath());
        $this->assertEquals($nyholm->getPath(), $phrity->getPath());
        $this->assertEquals($slim->getPath(), $phrity->getPath());

        // Check sub-delims
        $path = "/{$this->subdelims}";
        $phrity = $this->getPhrity()->withPath($path);
        $guzzle = $this->getGuzzle()->withPath($path);
        $lamias = $this->getLamias()->withPath($path);
        $league = $this->getLeague()->withPath($path);
        $nyholm = $this->getNyholm()->withPath($path);
        $slim = $this->getSlim()->withPath($path);

        // Guzzle, League and Nyholm do not encode sub-delims
        $this->assertEquals($guzzle->getPath(), $phrity->getPath());
        $this->assertEquals($league->getPath(), $phrity->getPath());
        $this->assertEquals($nyholm->getPath(), $phrity->getPath());
        // Lamias and Slim encode certain sub-delims
        $this->assertNotEquals($lamias->getPath(), $phrity->getPath());
        $this->assertNotEquals($slim->getPath(), $phrity->getPath());

        // Check unreserved
        $path = "/{$this->unreserved}";
        $phrity = $this->getPhrity()->withPath($path);
        $guzzle = $this->getGuzzle()->withPath($path);
        $lamias = $this->getLamias()->withPath($path);
        $league = $this->getLeague()->withPath($path);
        $nyholm = $this->getNyholm()->withPath($path);
        $slim = $this->getSlim()->withPath($path);

        // Guzzle, Lamias, League, Nyholm and Slim do not encode unreserved
        $this->assertEquals($guzzle->getPath(), $phrity->getPath());
        $this->assertEquals($lamias->getPath(), $phrity->getPath());
        $this->assertEquals($league->getPath(), $phrity->getPath());
        $this->assertEquals($nyholm->getPath(), $phrity->getPath());
        $this->assertEquals($slim->getPath(), $phrity->getPath());

        // Check pct
        $path = "/{$this->pct}/";
        $phrity = $this->getPhrity()->withPath($path);
        $guzzle = $this->getGuzzle()->withPath($path);
        $lamias = $this->getLamias()->withPath($path);
        $league = $this->getLeague()->withPath($path);
        $nyholm = $this->getNyholm()->withPath($path);
        $slim = $this->getSlim()->withPath($path);

        // Guzzle, Lamias, League, Nyholm and Slim keep pct
        $this->assertEquals($guzzle->getPath(), $phrity->getPath());
        $this->assertEquals($lamias->getPath(), $phrity->getPath());
        $this->assertEquals($league->getPath(), $phrity->getPath());
        $this->assertEquals($nyholm->getPath(), $phrity->getPath());
        $this->assertEquals($slim->getPath(), $phrity->getPath());

        // Check nonascii
        $path = "/{$this->nonascii}";
        $phrity = $this->getPhrity()->withPath($path);
        $guzzle = $this->getGuzzle()->withPath($path);
        $lamias = $this->getLamias()->withPath($path);
        $league = $this->getLeague()->withPath($path);
        $nyholm = $this->getNyholm()->withPath($path);
        $slim = $this->getSlim()->withPath($path);

        // Guzzle, League, Nyholm and Slim encode non-ascii
        $this->assertEquals($guzzle->getPath(), $phrity->getPath());
        $this->assertEquals($league->getPath(), $phrity->getPath());
        $this->assertEquals($nyholm->getPath(), $phrity->getPath());
        $this->assertEquals($slim->getPath(), $phrity->getPath());
        // Lamias do encode non-letter non-ascii
        $this->assertEquals($lamias->getPath(), $phrity->getPath(Phrity::URI_ENCODE));
    }

    public function testFragment(): void
    {
        // Check gen-delims
        $fragment = $this->gendelims;
        $phrity = $this->getPhrity()->withFragment($fragment);
        $guzzle = $this->getGuzzle()->withFragment($fragment);
        $lamias = $this->getLamias()->withFragment($fragment);
        $league = $this->getLeague()->withFragment($fragment);
        $nyholm = $this->getNyholm()->withFragment($fragment);
        $slim = $this->getSlim()->withFragment($fragment);

        // Guzzle, Lamias, League, Nyholm and Slim do encode some gen-delims
        $this->assertEquals($guzzle->getFragment(), $phrity->getFragment());
        $this->assertEquals($lamias->getFragment(), $phrity->getFragment());
        $this->assertEquals($league->getFragment(), $phrity->getFragment());
        $this->assertEquals($nyholm->getFragment(), $phrity->getFragment());
        $this->assertEquals($slim->getFragment(), $phrity->getFragment());

        // Check sub-delims
        $fragment = $this->subdelims;
        $phrity = $this->getPhrity()->withFragment($fragment);
        $guzzle = $this->getGuzzle()->withFragment($fragment);
        $lamias = $this->getLamias()->withFragment($fragment);
        $league = $this->getLeague()->withFragment($fragment);
        $nyholm = $this->getNyholm()->withFragment($fragment);
        $slim = $this->getSlim()->withFragment($fragment);

        // Guzzle, Lamias, League, Nyholm and Slim do not encode sub-delims
        $this->assertEquals($guzzle->getFragment(), $phrity->getFragment());
        $this->assertEquals($lamias->getFragment(), $phrity->getFragment());
        $this->assertEquals($league->getFragment(), $phrity->getFragment());
        $this->assertEquals($nyholm->getFragment(), $phrity->getFragment());
        $this->assertEquals($slim->getFragment(), $phrity->getFragment());

        // Check unreserved
        $fragment = $this->unreserved;
        $phrity = $this->getPhrity()->withFragment($fragment);
        $guzzle = $this->getGuzzle()->withFragment($fragment);
        $lamias = $this->getLamias()->withFragment($fragment);
        $league = $this->getLeague()->withFragment($fragment);
        $nyholm = $this->getNyholm()->withFragment($fragment);
        $slim = $this->getSlim()->withFragment($fragment);

        // Guzzle, Lamias, League, Nyholm and Slim do not encode unreserved
        $this->assertEquals($guzzle->getFragment(), $phrity->getFragment());
        $this->assertEquals($lamias->getFragment(), $phrity->getFragment());
        $this->assertEquals($league->getFragment(), $phrity->getFragment());
        $this->assertEquals($nyholm->getFragment(), $phrity->getFragment());
        $this->assertEquals($slim->getFragment(), $phrity->getFragment());

        // Check pct
        $fragment = $this->pct;
        $phrity = $this->getPhrity()->withFragment($fragment);
        $guzzle = $this->getGuzzle()->withFragment($fragment);
        $lamias = $this->getLamias()->withFragment($fragment);
        $league = $this->getLeague()->withFragment($fragment);
        $nyholm = $this->getNyholm()->withFragment($fragment);
        $slim = $this->getSlim()->withFragment($fragment);

        // Guzzle, Lamias, League, Nyholm and Slim keep pct
        $this->assertEquals($guzzle->getFragment(), $phrity->getFragment());
        $this->assertEquals($lamias->getFragment(), $phrity->getFragment());
        $this->assertEquals($league->getFragment(), $phrity->getFragment());
        $this->assertEquals($nyholm->getFragment(), $phrity->getFragment());
        $this->assertEquals($slim->getFragment(), $phrity->getFragment());

        // Check nonascii
        $fragment = $this->nonascii;
        $phrity = $this->getPhrity()->withFragment($fragment);
        $guzzle = $this->getGuzzle()->withFragment($fragment);
        $lamias = $this->getLamias()->withFragment($fragment);
        $league = $this->getLeague()->withFragment($fragment);
        $nyholm = $this->getNyholm()->withFragment($fragment);
        $slim = $this->getSlim()->withFragment($fragment);

        // Guzzle, League, Nyholm and Slim encode non-ascii
        $this->assertEquals($guzzle->getFragment(), $phrity->getFragment());
        $this->assertEquals($league->getFragment(), $phrity->getFragment());
        $this->assertEquals($nyholm->getFragment(), $phrity->getFragment());
        $this->assertEquals($slim->getFragment(), $phrity->getFragment());
        // Lamias do encode non-letter non-ascii
        $this->assertEquals($lamias->getPath(), $phrity->getPath(Phrity::URI_ENCODE));
    }

    public function testQuery(): void
    {
        // Check gen-delims
        $query = $this->gendelims;
        $phrity = $this->getPhrity()->withQuery($query);
        $guzzle = $this->getGuzzle()->withQuery($query);
        $league = $this->getLeague()->withQuery($query);
        $nyholm = $this->getNyholm()->withQuery($query);
        $slim = $this->getSlim()->withQuery($query);
        // Lamias do not allow gen-delims in path

        // Guzzle, League, Nyholm and Slim do encode some gen-delims
        $this->assertEquals($guzzle->getQuery(), $phrity->getQuery());
        $this->assertEquals($league->getQuery(), $phrity->getQuery());
        $this->assertEquals($nyholm->getQuery(), $phrity->getQuery());
        $this->assertEquals($slim->getQuery(), $phrity->getQuery());

        // Check sub-delims
        $query = $this->subdelims;
        $phrity = $this->getPhrity()->withQuery($query);
        $guzzle = $this->getGuzzle()->withQuery($query);
        $lamias = $this->getLamias()->withQuery($query);
        $league = $this->getLeague()->withQuery($query);
        $nyholm = $this->getNyholm()->withQuery($query);
        $slim = $this->getSlim()->withQuery($query);

        // Guzzle, Lamias, League, Nyholm and Slim do encode some gen-delims
        $this->assertEquals($guzzle->getQuery(), $phrity->getQuery());
        $this->assertEquals($lamias->getQuery(), $phrity->getQuery());
        $this->assertEquals($league->getQuery(), $phrity->getQuery());
        $this->assertEquals($nyholm->getQuery(), $phrity->getQuery());
        $this->assertEquals($slim->getQuery(), $phrity->getQuery());

        // Check unreserved
        $query = $this->unreserved;
        $phrity = $this->getPhrity()->withQuery($query);
        $guzzle = $this->getGuzzle()->withQuery($query);
        $lamias = $this->getLamias()->withQuery($query);
        $league = $this->getLeague()->withQuery($query);
        $nyholm = $this->getNyholm()->withQuery($query);
        $slim = $this->getSlim()->withQuery($query);

        // Guzzle, Lamias, League, Nyholm and Slim do encode some gen-delims
        $this->assertEquals($guzzle->getQuery(), $phrity->getQuery());
        $this->assertEquals($lamias->getQuery(), $phrity->getQuery());
        $this->assertEquals($league->getQuery(), $phrity->getQuery());
        $this->assertEquals($nyholm->getQuery(), $phrity->getQuery());
        $this->assertEquals($slim->getQuery(), $phrity->getQuery());

        // Check pct
        $query = $this->pct;
        $phrity = $this->getPhrity()->withQuery($query);
        $guzzle = $this->getGuzzle()->withQuery($query);
        $lamias = $this->getLamias()->withQuery($query);
        $league = $this->getLeague()->withQuery($query);
        $nyholm = $this->getNyholm()->withQuery($query);
        $slim = $this->getSlim()->withQuery($query);

        // Guzzle, Lamias, League, Nyholm and Slim keep pct
        $this->assertEquals($guzzle->getQuery(), $phrity->getQuery());
        $this->assertEquals($lamias->getQuery(), $phrity->getQuery());
        $this->assertEquals($league->getQuery(), $phrity->getQuery());
        $this->assertEquals($nyholm->getQuery(), $phrity->getQuery());
        $this->assertEquals($slim->getQuery(), $phrity->getQuery());

        // Check nonascii
        $query = $this->nonascii;
        $phrity = $this->getPhrity()->withQuery($query);
        $guzzle = $this->getGuzzle()->withQuery($query);
        $lamias = $this->getLamias()->withQuery($query);
        $league = $this->getLeague()->withQuery($query);
        $nyholm = $this->getNyholm()->withQuery($query);
        $slim = $this->getSlim()->withQuery($query);

        // Guzzle, League, Nyholm and Slim encode non-ascii
        $this->assertEquals($guzzle->getQuery(), $phrity->getQuery());
        $this->assertEquals($league->getQuery(), $phrity->getQuery());
        $this->assertEquals($nyholm->getQuery(), $phrity->getQuery());
        $this->assertEquals($slim->getQuery(), $phrity->getQuery());

        // Lamias do encode non-letter non-ascii
        $this->assertEquals($lamias->getPath(), $phrity->getPath(Phrity::URI_ENCODE));
    }

    private function getGuzzle(string $uri = ''): Guzzle
    {
        return new Guzzle($uri);
    }

    private function getLamias(string $uri = ''): Lamias
    {
        return new Lamias($uri);
    }

    private function getLeague(string $uri = ''): League
    {
        return League::createFromString($uri);
    }

    private function getNyholm(string $uri = ''): Nyholm
    {
        return new Nyholm($uri);
    }

    private function getSlim(string $uri = ''): Slim
    {
        return (new SlimFactory())->createUri($uri);
    }

    private function getPhrity(string $uri = ''): Phrity
    {
        return new Phrity($uri);
    }
}
