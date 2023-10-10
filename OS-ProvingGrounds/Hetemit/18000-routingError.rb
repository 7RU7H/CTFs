Routing Error
No route matches [GET] "/app/controllers"
Rails.root: /home/cmeeks/register_hetemit

Application Trace | Framework Trace | Full Trace
Routes
Routes match in priority from top to bottom

Helper	HTTP Verb	Path	Controller#Action
Path / Url		
Path Match
login_path	GET	/login(.:format)	
sessions#new

POST	/login(.:format)	
sessions#create

logout_path	GET	/logout(.:format)	
sessions#destroy

users_path	GET	/users(.:format)	
users#index

POST	/users(.:format)	
users#create

new_user_path	GET	/users/new(.:format)	
users#new

edit_user_path	GET	/users/:id/edit(.:format)	
users#edit

user_path	GET	/users/:id(.:format)	
users#show

PATCH	/users/:id(.:format)	
users#update

PUT	/users/:id(.:format)	
users#update

DELETE	/users/:id(.:format)	
users#destroy

root_path	GET	/	
page#index

rails_postmark_inbound_emails_path	POST	/rails/action_mailbox/postmark/inbound_emails(.:format)	
action_mailbox/ingresses/postmark/inbound_emails#create

rails_relay_inbound_emails_path	POST	/rails/action_mailbox/relay/inbound_emails(.:format)	
action_mailbox/ingresses/relay/inbound_emails#create

rails_sendgrid_inbound_emails_path	POST	/rails/action_mailbox/sendgrid/inbound_emails(.:format)	
action_mailbox/ingresses/sendgrid/inbound_emails#create

rails_mandrill_inbound_health_check_path	GET	/rails/action_mailbox/mandrill/inbound_emails(.:format)	
action_mailbox/ingresses/mandrill/inbound_emails#health_check

rails_mandrill_inbound_emails_path	POST	/rails/action_mailbox/mandrill/inbound_emails(.:format)	
action_mailbox/ingresses/mandrill/inbound_emails#create

rails_mailgun_inbound_emails_path	POST	/rails/action_mailbox/mailgun/inbound_emails/mime(.:format)	
action_mailbox/ingresses/mailgun/inbound_emails#create

rails_conductor_inbound_emails_path	GET	/rails/conductor/action_mailbox/inbound_emails(.:format)	
rails/conductor/action_mailbox/inbound_emails#index

POST	/rails/conductor/action_mailbox/inbound_emails(.:format)	
rails/conductor/action_mailbox/inbound_emails#create

new_rails_conductor_inbound_email_path	GET	/rails/conductor/action_mailbox/inbound_emails/new(.:format)	
rails/conductor/action_mailbox/inbound_emails#new

edit_rails_conductor_inbound_email_path	GET	/rails/conductor/action_mailbox/inbound_emails/:id/edit(.:format)	
rails/conductor/action_mailbox/inbound_emails#edit

rails_conductor_inbound_email_path	GET	/rails/conductor/action_mailbox/inbound_emails/:id(.:format)	
rails/conductor/action_mailbox/inbound_emails#show

PATCH	/rails/conductor/action_mailbox/inbound_emails/:id(.:format)	
rails/conductor/action_mailbox/inbound_emails#update

PUT	/rails/conductor/action_mailbox/inbound_emails/:id(.:format)	
rails/conductor/action_mailbox/inbound_emails#update

DELETE	/rails/conductor/action_mailbox/inbound_emails/:id(.:format)	
rails/conductor/action_mailbox/inbound_emails#destroy

rails_conductor_inbound_email_reroute_path	POST	/rails/conductor/action_mailbox/:inbound_email_id/reroute(.:format)	
rails/conductor/action_mailbox/reroutes#create

rails_service_blob_path	GET	/rails/active_storage/blobs/:signed_id/*filename(.:format)	
active_storage/blobs#show

rails_blob_representation_path	GET	/rails/active_storage/representations/:signed_blob_id/:variation_key/*filename(.:format)	
active_storage/representations#show

rails_disk_service_path	GET	/rails/active_storage/disk/:encoded_key/*filename(.:format)	
active_storage/disk#show

update_rails_disk_service_path	PUT	/rails/active_storage/disk/:encoded_token(.:format)	
active_storage/disk#update

rails_direct_uploads_path	POST	/rails/active_storage/direct_uploads(.:format)	
active_storage/direct_uploads#create

Request
Parameters:

None
Toggle session dump
_csrf_token: "YxCKr3W7K0gatLan4IYhEvqKoMHgZiB/ZPR2x2XO53s="
session_id: "cb519049c2ac23aabba840a556f1644a"
Toggle env dump
GATEWAY_INTERFACE: "CGI/1.2"
HTTP_ACCEPT: "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7"
HTTP_ACCEPT_ENCODING: "gzip, deflate, br"
HTTP_ACCEPT_LANGUAGE: "en-GB,en-US;q=0.9,en;q=0.8"
HTTP_VERSION: "HTTP/1.1"
ORIGINAL_SCRIPT_NAME: ""
REMOTE_ADDR: "192.168.45.167"
SERVER_NAME: "192.168.236.117"
SERVER_PROTOCOL: "HTTP/1.1"
Response
Headers:

None
