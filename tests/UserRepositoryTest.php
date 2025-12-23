<?php

require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/DatabaseTestCase.php';

class UserRepositoryTest extends DatabaseTestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    public function testFindByEmailOrUsernameFindsByEmail(): void
    {
        $this->seedUser(['email' => 'test@example.com', 'username' => 'otheruser']);
        
        $user = $this->repository->findByEmailOrUsername('test@example.com');
        
        $this->assertNotNull($user);
        $this->assertSame('test@example.com', $user['email']);
    }

    public function testFindByEmailOrUsernameFindsByUsername(): void
    {
        $this->seedUser(['email' => 'other@example.com', 'username' => 'targetuser']);
        
        $user = $this->repository->findByEmailOrUsername('targetuser');
        
        $this->assertNotNull($user);
        $this->assertSame('targetuser', $user['username']);
    }

    public function testFindByEmailOrUsernameReturnsNullWhenNotFound(): void
    {
        $user = $this->repository->findByEmailOrUsername('nonexistent');
        $this->assertNull($user);
    }

    public function testFindByIdReturnsUser(): void
    {
        $id = $this->seedUser(['full_name' => 'John Doe']);
        
        $user = $this->repository->findById($id);
        
        $this->assertNotNull($user);
        $this->assertSame('John Doe', $user['full_name']);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $user = $this->repository->findById(99999);
        $this->assertNull($user);
    }

    public function testFindByIdSelectsSpecificFields(): void
    {
        $id = $this->seedUser(['username' => 'fieldtest']);
        
        $user = $this->repository->findById($id, ['id', 'username']);
        
        $this->assertNotNull($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('username', $user);
        $this->assertArrayNotHasKey('email', $user);
        $this->assertArrayNotHasKey('password', $user);
    }
}
