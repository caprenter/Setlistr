Author: Alice Kaerast


==Setlistr Ruby==

A Ruby library to provide basic integration with the Setlistr api.

This is designed primarily as an example of how to use the api rather than a complete working library.

The tests run against the live site at setlistr.co.uk which is currently hardcoded into the library.

==Usage==
Install Ruby (1.9.x preferred)
(on Xubuntu 12.04 I installed ruby (1.9.3) via synaptic)

Open a terminal and run:
  gem install bundler

Then change into the library directory
  cd <path to> contrib/setlistr_ruby

Run: 
  bundle install
  
Run the tests:
  bundle exec rspec

