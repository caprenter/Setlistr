require File.dirname(__FILE__) + '/../lib/setlistr.rb'


describe Setlistr do
  before(:all) do
    @setlist = Setlistr::Setlist.new(686)
  end

  it "has an ID" do
    @setlist.id.should eq(686)
  end
  
  it "loads the correct title" do
    @setlist.title.should eq("Setlist for Friday 22nd at Shipley")
  end
  
  it "has a last updated date" do
    @setlist.last_updated.to_s.should eq("2012-03-02T18:48:35+00:00")
  end
  
  it "should have songs" do
    @setlist.in_set.length.should eq(4)
    @setlist.not_in_set.length.should eq(1)
  end
  
  it "gracefully handles non-existant sets" do
    begin
      failsetlist = Setlistr::Setlist.new(1)
    rescue Exception => e
      error = e.message
    ensure
      error.should eq("Setlist not found")
    end
  end    
  
  it "gives at least one list" do
    lists = Setlistr.all
    lists[0].class.should eq(Hash)
    lists[0]['name'].class.should eq(String)    
  end
  
  it "limits lists by username" do
    lists = Setlistr.byuser('caprenter')
    lists[0].class.should eq(Hash)
    lists[0]['name'].class.should eq(String)    
  end
end
