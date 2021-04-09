<?php

namespace App\Service;

use App\Entity\Server;
use App\Entity\SshKey;
use App\Entity\User;

class SshAuthService
{
    protected const KEY_BEGIN = '### START Key %s';
    protected const KEY_END = '### END Key %s';

    /**
     * @var string
     */
    private string $privateSshKey;

    /**
     * @var false|resource
     */
    private $connection;

    /**
     * @var string
     */
    private string $publicSshKey;

    /**
     * SshAuthService constructor.
     *
     * @param string $privateSshKey
     * @param string $publicSshKey
     */
    public function __construct(string $privateSshKey, string $publicSshKey)
    {
        $this->privateSshKey = $privateSshKey;
        $this->publicSshKey = $publicSshKey;
    }

    /**
     * @param User $user
     */
    public function create(User $user): void
    {
        foreach ($user->getServers() as $server) {
            foreach ($user->getSshKeys() as $sshKey) {
                if ($sshKey->isActive()) {
                    $this->createAuth($server, $user->getUsername(), $sshKey);
                }
            }
        }
    }

    /**
     * @param User $user
     */
    public function update(User $user): void
    {
        foreach ($user->getServers() as $server) {
            $this->removeAuth($server, $user->getUsername());
            foreach ($user->getSshKeys() as $sshKey) {
                if ($sshKey->isActive()) {
                    $this->createAuth($server, $user->getUsername(), $sshKey);
                }
            }
        }
    }

    /**
     * @param User $user
     */
    public function remove(User $user): void
    {
        foreach ($user->getServers() as $server) {
            $this->removeAuth($server, $user->getUsername());
        }
    }

    /**
     * @param Server $server
     * @param string $username
     */
    protected function removeAuth(Server $server, string $username): void
    {
        $this->connect($server);

        $this->exec('sed -i  "/### START Key ' . $username . '/,+2d" ~/.ssh/authorized_keys ');

        $this->disconnect();
    }

    /**
     * @param Server $server
     * @param string $username
     * @param SshKey $key
     */
    protected function createAuth(Server $server, string $username, SshKey $key): void
    {
        $this->connect($server);

        $this->exec(\sprintf('echo "' . self::KEY_BEGIN . '" >> ~/.ssh/authorized_keys', $username, $key->getName()));
        $this->exec('echo "' . $key->getPublicKey() . '" >> ~/.ssh/authorized_keys');
        $this->exec(\sprintf('echo "' . self::KEY_END . '" >> ~/.ssh/authorized_keys', $username));

        $this->disconnect();
    }

    /**
     * @param Server $server
     */
    protected function connect(Server $server): void
    {
        $this->connection = \ssh2_connect($server->getHostname(), $server->getPort());
        if ($this->connection === false) {
            throw new \RuntimeException('Cannot connect to server');
        }

        if (!\ssh2_auth_pubkey_file($this->connection, $server->getUsername(), $this->publicSshKey, $this->privateSshKey)) {
            throw new \RuntimeException('Autentication rejected by server');
        }
    }

    /**
     * @param string $cmd
     * @return string
     */
    protected function exec(string $cmd): string
    {
        if (!($stream = \ssh2_exec($this->connection, $cmd))) {
            throw new \RuntimeException('SSH command failed');
        }
        \stream_set_blocking($stream, true);
        $data = "";
        while ($buf = \fread($stream, 4096)) {
            $data .= $buf;
        }
        \fclose($stream);
        return $data;
    }

    /**
     * @return void
     */
    protected function disconnect(): void
    {
        $this->exec('echo "EXITING" && exit;');
        $this->connection = null;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
