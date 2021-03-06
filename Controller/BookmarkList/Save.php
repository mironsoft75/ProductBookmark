<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 5/13/19
 * Time: 3:26 PM
 */

namespace Inchoo\ProductBookmark\Controller\BookmarkList;

use Inchoo\ProductBookmark\Controller\AbstractAction;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;

class Save extends AbstractAction
{
    /**
     * @var \Inchoo\ProductBookmark\Api\BookmarkListRepositoryInterface
     */
    private $bookmarkListRepository;
    /**
     * @var Http
     */
    private $request;
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $validator;

    public function __construct(
        Context $context,
        Session $session,
        \Inchoo\ProductBookmark\Api\BookmarkListRepositoryInterface $bookmarkListRepository,
        Http $request,
        \Magento\Framework\Data\Form\FormKey\Validator $validator
    ) {
        parent::__construct($context, $session);
        $this->bookmarkListRepository = $bookmarkListRepository;
        $this->request = $request;
        $this->validator = $validator;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $this->isLoggedIn();
        if (!$this->validator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage('Invalid form key.');
            return $this->_redirect('bookmark/bookmarklist/bookmarklist');
        }
        try {
            $customerId = $this->session->getCustomerId();
            $content = $this->request->getPostValue('title');
            $this->bookmarkListRepository->saveToDb($content, $customerId);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Could not create new bookmark list.'));
        }

        $this->messageManager->addSuccessMessage(__('Bookmark list successfully saved.'));
        return $this->_redirect('bookmark/bookmarklist/bookmarklist');
    }
}
