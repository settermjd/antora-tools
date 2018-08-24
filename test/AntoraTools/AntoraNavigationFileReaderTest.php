<?php
declare(strict_types=1);

namespace AntoraToolsTest;

use AntoraTools\NavigationFileReader;
use PHPUnit\Framework\TestCase;

class NavigationFileReaderTest extends TestCase
{
	public function testCanFindAllReferencedFilesInANavigationFile()
	{
		$fileContents =<<<EOF
* xref:index.adoc[User Manual]
** xref:whats_new.adoc[What's New]
** xref:files/webgui/overview.adoc[The WebUI]
*** xref:webinterface.adoc[Web Interface]
*** xref:userpreferences.adoc[User Preferences]
*** xref:files/webgui/navigating.adoc[Navigating the WebUI]
*** xref:files/webgui/comments.adoc[Comments]
*** xref:files/webgui/custom_groups.adoc[Custom Groups]
*** Files
**** xref:files/access_webdav.adoc[Access WebDAV]
**** xref:files/webgui/sharing.adoc[Sharing Files]
**** xref:files/webgui/tagging.adoc[Tagging Files]
**** xref:files/encrypting_files.adoc[Encrypting Files]
**** xref:files/deleted_file_management.adoc[Managing Deleted Files]
**** xref:files/large_file_upload.adoc[Large File Uploads]
*** xref:files/public_link_shares.adoc[Public Link Shares]
*** xref:files/federated_cloud_sharing.adoc[Federated Cloud Sharing]
*** xref:session_management.adoc[Managing Connected Browsers and Devices]
*** xref:files/version_control.adoc[Version Control]
*** Storage
**** xref:files/webgui/quota.adoc[Storage Quotas]
**** xref:external_storage/external_storage.adoc[External Storage]
**** xref:external_storage/sharepoint_connecting.adoc[Connecting to SharePoint]
** xref:files/desktop_mobile_sync.adoc[Desktop Mobile Sync]
** Apps
*** xref:files/gallery_app.adoc[Gallery App]
*** xref:pim/calendar.adoc[Calendar]
*** xref:pim/contacts.adoc[Contacts]
** Synchronization Clients
*** xref:pim/sync_ios.adoc[Sync iOS]
*** xref:pim/sync_kde.adoc[Sync KDE]
*** xref:pim/sync_osx.adoc[Sync OSX]
*** xref:pim/sync_thunderbird.adoc[Sync Thunderbird]
** xref:troubleshooting.adoc[Troubleshooting]
EOF;

		$navFileReader = new NavigationFileReader($fileContents);
		$files = $navFileReader->parseNavigationFile();
		$this->assertCount(30, $files);
	}

	public function testReturnsEmptyArrayIfNoLinksAreFoundInTheNavigationFile()
	{
		$fileContents =<<<EOF
*** Files
*** xref:pim/sync_osx.adc[Sync OSX]
EOF;

		$navFileReader = new NavigationFileReader($fileContents);
		$files = $navFileReader->parseNavigationFile();
		$this->assertCount(0, $files);
	}
}