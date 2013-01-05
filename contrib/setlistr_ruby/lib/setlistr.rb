require 'json'
require "net/http"
require "uri"

module Setlistr

  def all
    uri = URI.parse("http://www.setlistr.co.uk/api/?list=all")
    response = Net::HTTP.get_response(uri)
    body = JSON.parse(response.body)
    return body
  end
  module_function :all
  
  def byuser(user)
    uri = URI.parse("http://www.setlistr.co.uk/api/?list=all&username=#{user.to_s}")
    response = Net::HTTP.get_response(uri)
    body = JSON.parse(response.body)
    return body
  end
  module_function :byuser

  class Setlist
    attr :id, :title, :last_updated, :in_set, :not_in_set
    def initialize(id)
      @id = id
      uri = URI.parse("http://www.setlistr.co.uk/api/?list=#{@id.to_s}")
      response = Net::HTTP.get_response(uri)
      body = JSON.parse(response.body)
      unless body[0].has_key?('title')
        raise "Setlist not found"
      end
      @title = body[0]['title']
      @last_updated = body[0]['last_updated']
      @in_set = body[0]['in_set']
      @not_in_set = body[0]['not_in_set']
    end
  end
end
