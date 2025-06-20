import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TrainingCvsComponent } from './training-cvs.component';

describe('TrainingCvsComponent', () => {
  let component: TrainingCvsComponent;
  let fixture: ComponentFixture<TrainingCvsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TrainingCvsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(TrainingCvsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
