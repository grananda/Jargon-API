<?php

namespace Tests\Unit\Listeners;


use App\Events\CollaboratorAddedToOrganization;
use App\Listeners\SendOrganizationCollaboratorInvitationEmail;
use App\Mail\SendOrganizationCollaboratorEmail;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class SendOrganizationCollaboratorInvitationEmailTest extends TestCase
{
    /** @test */
    public function email_is_sent_after_collaborator_is_added_to_organization()
    {
        // Given
        Mail::fake();

        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();

        $invitationToken = Str::random(Organization::ITEM_TOKEN_LENGTH);

        /** @var CollaboratorAddedToOrganization $event */
        $event = new CollaboratorAddedToOrganization($organization, $user, $invitationToken);

        /** @var \App\Mail\SendOrganizationCollaboratorEmail $listener */
        $listener = new SendOrganizationCollaboratorInvitationEmail();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendOrganizationCollaboratorEmail::class);
    }
}
