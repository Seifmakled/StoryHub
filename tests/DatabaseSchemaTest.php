<?php

require_once __DIR__ . '/DatabaseTestCase.php';

class DatabaseSchemaTest extends DatabaseTestCase
{
    public function testTablesAreCreatedInTestMode(): void
    {
        $tables = ['users','articles','likes','bookmarks','comments','follows','notifications'];
        foreach ($tables as $table) {
            $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}'");
            $this->assertNotFalse($stmt->fetch(), "Table {$table} should exist");
        }
    }
}
