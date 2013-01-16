require 'json'
require "net/http"
require "uri"

# @author Alice Kaerast <alice@kaerast.info>
# @note This library is incomplete and designed as an example implementation only
# Module for interacting with the setlistr.co.uk api
module Setlistr
  # @!attr [w] key
  #  API key
  @key = nil
  # @!attr [w] username
  #  Username to connect using
  @username = nil

  # @param [String] key the key to be used when interacting with the api
  def key=(key)
    @key = key
  end
  module_function :key=

  # @param [String] username the username to be used when interacting with the api
  def username=(username)
    @username = username
  end
  module_function :username=

  # @return [Array] hashes of individual setlists
  # @see byuser(user)
  def all
    uri = URI.parse("http://www.setlistr.co.uk/api/?list=all")
    response = Net::HTTP.get_response(uri)
    body = JSON.parse(response.body)
    return body
  end
  module_function :all

  # @param [String] user the username to load setlists for
  # @see all
  def byuser(user)
    uri = "http://www.setlistr.co.uk/api/?list=all&username=#{user.to_s}"
    uri << "&key=#{@key.to_s}" unless @key == nil
    response = Net::HTTP.get_response(URI.parse(uri))
    body = JSON.parse(response.body)
    return body
  end
  module_function :byuser

  # An individual setlist
  class Setlist
    attr :id, :title, :last_updated, :in_set, :not_in_set, :username

    # @param [Fixnum] id the id number of the setlist
    # @return [Setlistr::Setlist] a setlist object
    # @raise [Setlist not found] if the setlist doesn't exist
    # @todo We should use lazyloading and not call the api until we need to
    #  that way we can cheaply create many Setlist objects from all() or byuser(user)
    def initialize(id)
      @id = id
      uri = "http://www.setlistr.co.uk/api/?list=#{@id.to_s}"
      uri << "&key=#{@key.to_s}" unless @key == nil
      response = Net::HTTP.get_response(URI.parse(uri))
      body = JSON.parse(response.body)
      unless body[0].has_key?('title')
        raise "Setlist not found"
      end
      @title = body[0]['title']
      @last_updated = body[0]['last_updated']
      @in_set = body[0]['in_set']
      @not_in_set = body[0]['not_in_set']
      @username = body[0]['username']
    end
  end
end
