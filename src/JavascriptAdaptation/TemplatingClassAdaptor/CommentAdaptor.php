<?php
namespace App\JavascriptAdaptation\TemplatingClassAdaptor;

use App\Entity\Comment;

class CommentAdaptor
{
    /**
     * @param Comment
     * @return array
     */
    public function adapte($comment)
    {
        return [
            'createdAt' => $comment->getCreatedAt()->format('d/m/Y'),
            'user' => $comment->getUser()->getUsername(),
            'content' => $comment->getContent()
        ];
    }

    /**
     * @param Comment[]
     * @return array
     */
    public function adapteAll($comments)
    {
        return array_map(function($comment) {
            /** @var Comment */
            $comment = $comment;
            return [
                'createdAt' => $comment->getCreatedAt()->format('d/m/Y'),
                'user' => $comment->getUser()->getUsername(),
                'content' => $comment->getContent()
            ];
        }, $comments);
    }
}