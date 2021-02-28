<?php

namespace App\Service;

use App\Entity\Server;
use App\Entity\User;

class SshAuthService
{
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
     * @param string $privateSshKey
     */
    public function __construct(string $privateSshKey, string $publicSshKey)
    {
        $this->privateSshKey = $privateSshKey;
        $this->publicSshKey = $publicSshKey;
    }

    public function create(User $user)
    {
        foreach ($user->getServers() as $server) {
            foreach ($user->getSshKeys() as $sshKey) {
                if ($sshKey->isActive()) {
                    $this->createAuth($server, $user->getUsername(), $sshKey->getPublicKey());
                }
            }
        }
    }

    public function update(User $user)
    {

    }

    public function remove(User $user)
    {
        foreach ($user->getServers() as $server) {
            $this->removeAuth($server, $user->getUsername());
        }
    }

    protected function removeAuth(Server $server, string $username)
    {
        $this->connect($server);

        $this->exec('sed -i  "/### START Key ' . $username . '/,+2d" ~/.ssh/authorized_keys ');

        $this->disconnect();
    }

    protected function createAuth(Server $server, string $username, string $key)
    {
        $this->connect($server);

        $this->exec('echo "### START Key ' . $username . '" >> ~/.ssh/authorized_keys');
        $this->exec('echo "' . $key . '" >> ~/.ssh/authorized_keys');
        $this->exec('echo "### END Key ' . $username . '" >> ~/.ssh/authorized_keys');

        $this->disconnect();
    }

    protected function connect(Server $server): void
    {
        $this->connection = \ssh2_connect($server->getHostname(), $server->getPort());
        if ($this->connection === false) {
            throw new \RuntimeException('Cannot connect to server');
        }

        if (!\ssh2_auth_pubkey_file($this->connection, $server->getUsername(), $this->publicSshKey , $this->privateSshKey)) {
            throw new \RuntimeException('Autentication rejected by server');
        }
    }

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

    protected function disconnect() {
        $this->exec('echo "EXITING" && exit;');
        $this->connection = null;
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}