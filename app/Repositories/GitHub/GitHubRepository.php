<?php

namespace App\Repositories\GitHub;

use App\Exceptions\GitHubConnectionException;
use App\Models\Translations\Project;
use Github\Client;
use GrahamCampbell\GitHub\GitHubManager;

class GitHubRepository
{
	/**
	 * @var \GrahamCampbell\GitHub\GitHubManager
	 */
	private $gitHubManager;

	/**
	 * @var string
	 */
	private $authType;

	/**
	 * GitHubRepository constructor.
	 *
	 * @param \GrahamCampbell\GitHub\GitHubManager $gitHubManager
	 */
	public function __construct(GitHubManager $gitHubManager)
	{
		$this->gitHubManager = $gitHubManager;

		$this->authType = Client::AUTH_HTTP_TOKEN;
	}

	/**
	 * @param \App\Models\Translations\Project $project
	 *
	 * @return array
	 * @throws \App\Exceptions\GitHubConnectionException
	 *
	 */
	public function getRepositoryList(Project $project)
	{
		$this->authenticate($project);

		return $this->gitHubManager->user()->myRepositories();
	}

	/**
	 * @param \App\Models\Translations\Project $project
	 *
	 * @param string                           $branch
	 *
	 * @return array
	 * @throws \App\Exceptions\GitHubConnectionException
	 */
	public function getRepositoryDetails(Project $project, string $branch)
	{
		$this->authenticate($project);

		return $this->gitHubManager->repo()->show($project->gitHubConfig->username, $branch);
	}

	public function createBranch(Project $project, string $branch)
	{
		$this->authenticate($project);

		/** @var array $reference */
		$reference = $this->getBaseBranchReferenceDetails($project, $project->gitHubConfig->base_branch);

		return $this->gitHubManager->gitData()->references()->create($project->gitHubConfig->username, $project->gitHubConfig->repository, [
			'ref' => 'refs/' . $branch,
			'sha' => $reference['object']['sha'],
		]);
	}

	/**
	 * @param \App\Models\Translations\Project $project
	 *
	 * @return array
	 * @throws \App\Exceptions\GitHubConnectionException
	 */
	private function getBaseBranchReferenceDetails(Project $project)
	{
		$this->authenticate($project);

		return $this->gitHubManager->gitData()->references()->show(
			$project->gitHubConfig->username,
			$project->gitHubConfig->repository,
			'heads/' . $project->gitHubConfig->base_branch
		);
	}

	/**
	 * @param \App\Models\Translations\Project $project
	 *
	 * @throws \App\Exceptions\GitHubConnectionException
	 */
	private function authenticate(Project $project): void
	{
		if (!$project->hasGitHubAccess()) {
			throw new GitHubConnectionException(trans('Project GitHub configuration missing'));
		}

		$this->gitHubManager->authenticate($project->gitHubConfig->access_token, Client::AUTH_HTTP_TOKEN);
	}
}
