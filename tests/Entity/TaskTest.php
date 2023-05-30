<?php

namespace Tests\App\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Task;

class TaskTest extends TestCase
{
    public function testSetTitle()
    {
        $task = new Task();
        $title = "Test Title";
        
        $task->setTitle($title);
        
        $this->assertEquals($title, $task->getTitle());
    }
    
    public function testSetContent()
    {
        $task = new Task();
        $content = "Test Content";
        
        $task->setContent($content);
        
        $this->assertEquals($content, $task->getContent());
    }
    
    public function testSetCreatedAt()
    {
        $task = new Task();
        $createdAt = new \DateTime();
        
        $task->setCreatedAt($createdAt);
        
        $this->assertEquals($createdAt, $task->getCreatedAt());
    }
    
    public function testGetCreatedAt()
    {
        $task = new Task();
        
        $createdAt = $task->getCreatedAt();
        
        $this->assertInstanceOf(\DateTimeInterface::class, $createdAt);
    }
    
    public function testToggle()
    {
        $task = new Task();
        
        $task->toggle(true);
        
        $this->assertTrue($task->isDone());
        
        $task->toggle(false);
        
        $this->assertFalse($task->isDone());
    }
}
