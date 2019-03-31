<?php

namespace TransactionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction", indexes={@ORM\Index(name="transaction_category_index", columns={"category_id"})})
 * @ORM\Entity(repositoryClass="TransactionBundle\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Transaction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=30, nullable=false)
     * @Assert\NotBlank(message="Title should not be empty")
     */
    private $title = '';

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", precision=10, scale=0, nullable=false)
     */
    private $amount = 0.00;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_input", type="boolean", nullable=false)
     */
    private $isInput = false;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     */
    private $description = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean", nullable=false)
     */
    private $isValid = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * })
     */
    private $category;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="TransactionBundle\Entity\TransactionTag", mappedBy="transaction",cascade={"persist","remove"},orphanRemoval=true)
     */
    private $transactionTags;

    public $tags;

    /**
     * Transaction constructor.
     * @param int $id
     */
    public function __construct()
    {
        $this->category = new Category();
        $this->createdAt = new \DateTime();
        $this->transactionTags = new ArrayCollection();
    }

    /**²
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return bool
     */
    public function isInput(): bool
    {
        return $this->isInput;
    }

    /**
     * @param bool $isInput
     */
    public function setIsInput(bool $isInput)
    {
        $this->isInput = $isInput;
    }



    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @param bool $isValid
     */
    public function setIsValid(bool $isValid)
    {
        $this->isValid = $isValid;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return PersistentCollection
     */
    public function getTransactionTags()
    {
        return $this->transactionTags;
    }
    public function addTransactionTag(TransactionTag $transactionTag)
    {
        $this->transactionTags->add($transactionTag);
        return $this;
    }

    public function removeTransactionTag(TransactionTag $transactionTag)
    {
        $this->transactionTags->removeElement($transactionTag);
        return $this;
    }
    /**
     * @param mixed $transactionTags
     */
    public function setTransactionTags($transactionTags)
    {
        $this->transactionTags = $transactionTags;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setTransactionTagsRelation()
    {
        if($this->tags){
            foreach ($this->tags as $tag){
                $transactionTag = new TransactionTag();
                $transactionTag->setTag($tag);
                $transactionTag->setTransaction($this);
                $this->addTransactionTag($transactionTag);
            }
        }
    }

    public function getStringTags(){
        $tags = array();
        foreach ($this->getTransactionTags() as $transactionTag){
            $tags[] = $transactionTag->getTag()->getName();
        }
        return implode(',',$tags);
    }

    public function getTags()
    {
        $tags = new ArrayCollection();
        foreach($this->transactionTags as $transactionTag)
        {
            $tags[] = $transactionTag->getTag();
        }
        return $tags;
    }

}

